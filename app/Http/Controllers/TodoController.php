<?php
namespace App\Http\Controllers;

use App\Models\{Todo, Project, Tag, SubTask, TodoHistory};
use Illuminate\Http\Request;
use Carbon\Carbon;

class TodoController extends Controller
{
    public function index(Request $request)
    {
        $query = Todo::with(['project','tags','subTasks','attachments']);

        // Filters
        if ($request->search)   $query->where('title','like',"%{$request->search}%");
        if ($request->status)   $query->where('status', $request->status);
        if ($request->priority) $query->where('priority', $request->priority);
        if ($request->project)  $query->where('project_id', $request->project);
        if ($request->tag)      $query->whereHas('tags', fn($q) => $q->where('tags.id',$request->tag));
        if ($request->due)      $query->whereDate('due_date', $request->due);
        if ($request->overdue)  $query->where('due_date','<',now())->where('completed',false);

        $todos    = $query->orderBy('position')->orderByDesc('created_at')->paginate(20);
        $projects = Project::where('status','active')->get();
        $tags     = Tag::all();

$stats = [
    'total'       => Todo::count(),
    'completed'   => Todo::where('completed', true)->count(),
    'pending'     => Todo::where('status', 'pending')->count(),
    'in_progress' => Todo::where('status', 'in_progress')->count(),
    'overdue'     => Todo::where('due_date', '<', now())
                         ->where('completed', false)->count(),
];

        return view('todos.index', compact('todos','projects','tags','stats'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:high,medium,low',
            'status'      => 'required|in:pending,in_progress,completed,archived',
            'category'    => 'required|in:general,work,personal,health,shopping',
            'project_id'  => 'nullable|exists:projects,id',
            'due_date'    => 'nullable|date',
            'due_time'    => 'nullable',
            'recurring'   => 'nullable|in:none,daily,weekly,monthly',
            'tags'        => 'nullable|array',
        ]);

        $todo = Todo::create($data);
        if ($request->tags) $todo->tags()->sync($request->tags);

        TodoHistory::create([
            'todo_id'=>$todo->id,'field_changed'=>'status',
            'old_value'=>null,'new_value'=>$todo->status,'action'=>'created'
        ]);

        return redirect()->route('todos.index')->with('success','Task created! 🎉');
    }

    public function show(Todo $todo)
    {
        $todo->load(['project','tags','subTasks','comments','attachments','history']);
        return view('todos.show', compact('todo'));
    }

    public function update(Request $request, Todo $todo)
    {
        $data = $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority'    => 'required|in:high,medium,low',
            'status'      => 'required|in:pending,in_progress,completed,archived',
            'category'    => 'required|in:general,work,personal,health,shopping',
            'project_id'  => 'nullable|exists:projects,id',
            'due_date'    => 'nullable|date',
            'due_time'    => 'nullable',
            'recurring'   => 'nullable|in:none,daily,weekly,monthly',
            'tags'        => 'nullable|array',
        ]);

        // Log history
        foreach (['title','status','priority'] as $field) {
            if (isset($data[$field]) && $todo->$field !== $data[$field]) {
                TodoHistory::create([
                    'todo_id'=>$todo->id,'field_changed'=>$field,
                    'old_value'=>$todo->$field,'new_value'=>$data[$field],'action'=>'updated'
                ]);
            }
        }

        $todo->update($data);
        if ($request->has('tags')) $todo->tags()->sync($request->tags ?? []);

        return redirect()->route('todos.index')->with('success','Task updated! ✏️');
    }

    public function destroy(Todo $todo)
    {
        $todo->delete();
        return response()->json(['deleted'=>true]);
    }

    public function toggle(Todo $todo)
    {
        $todo->update([
            'completed' => !$todo->completed,
            'status'    => !$todo->completed ? 'completed' : 'pending',
        ]);
        return response()->json(['completed'=>$todo->completed]);
    }

public function updateStatus(Request $request, Todo $todo)
{
    $request->validate([
        'status' => 'required|in:pending,in_progress,completed,archived'
    ]);

    $todo->update([
        'status'    => $request->status,
        'completed' => $request->status === 'completed' ? true : false,
    ]);

    return response()->json([
        'ok'        => true,
        'status'    => $todo->status,
        'completed' => $todo->completed,
    ]);
}


    public function updatePosition(Request $request, Todo $todo)
    {
        $todo->update(['position' => $request->position]);
        return response()->json(['ok'=>true]);
    }

    public function kanban()
    {
        $statuses = ['pending','in_progress','completed','archived'];
        $board = [];
        foreach ($statuses as $s) {
            $board[$s] = Todo::where('status',$s)->with(['tags','project'])->orderBy('position')->get();
        }
        return view('todos.kanban', compact('board'));
    }

    public function calendar()
    {
        $todos = Todo::whereNotNull('due_date')->with('project')->get();
        $events = $todos->map(fn($t) => [
            'id'    => $t->id,
            'title' => $t->title,
            'start' => $t->due_date->format('Y-m-d'),
            'color' => match($t->priority){ 'high'=>'#fa4d6d','medium'=>'#fac74d',default=>'#4dfaa0' },
            'url'   => route('todos.show',$t->id),
        ]);
        return view('todos.calendar', compact('events'));
    }

    // Sub-tasks
    public function storeSubtask(Request $request, Todo $todo)
    {
        $sub = $todo->subTasks()->create(['title'=>$request->title]);
        return response()->json($sub);
    }
    public function toggleSubtask(Todo $todo, SubTask $sub)
    {
        $sub->update(['completed'=>!$sub->completed]);
        return response()->json(['completed'=>$sub->completed,'progress'=>$todo->subTaskProgress()]);
    }
    public function deleteSubtask(Todo $todo, SubTask $sub)
    {
        $sub->delete();
        return response()->json(['ok'=>true]);
    }
    public function stats()
{
    return response()->json([
        'total'       => Todo::count(),
        'completed'   => Todo::where('completed', true)->count(),
        'pending'     => Todo::where('status', 'pending')->count(),
        'in_progress' => Todo::where('status', 'in_progress')->count(),
        'overdue'     => Todo::where('due_date', '<', now())->where('completed', false)->count(),
    ]);
}
}
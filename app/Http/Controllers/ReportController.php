<?php
namespace App\Http\Controllers;
use App\Models\{Todo, Project};
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index()
    {
        $stats = [
            'total'       => Todo::count(),
            'completed'   => Todo::where('completed',true)->count(),
            'pending'     => Todo::where('status','pending')->count(),
            'in_progress' => Todo::where('status','in_progress')->count(),
            'overdue'     => Todo::where('due_date','<',now())->where('completed',false)->count(),
            'high'        => Todo::where('priority','high')->count(),
        ];
        $byProject  = Project::withCount(['todos','todos as done_count'=>fn($q)=>$q->where('completed',true)])->get();
        $byCategory = Todo::selectRaw('category, count(*) as count')->groupBy('category')->get();
        $recentDone = Todo::where('completed',true)->latest()->take(10)->get();
        $overdue    = Todo::where('due_date','<',now())->where('completed',false)->with('project')->get();

        return view('reports.index', compact('stats','byProject','byCategory','recentDone','overdue'));
    }

    public function exportCsv()
    {
        $todos = Todo::with(['project','tags'])->get();
        $headers = ['Content-Type'=>'text/csv','Content-Disposition'=>'attachment; filename="tasks.csv"'];
        $callback = function() use ($todos) {
            $f = fopen('php://output','w');
            fputcsv($f, ['ID','Title','Status','Priority','Category','Project','Due Date','Completed','Tags']);
            foreach ($todos as $t) {
                fputcsv($f, [
                    $t->id, $t->title, $t->status, $t->priority,
                    $t->category, $t->project?->name ?? '-',
                    $t->due_date?->format('Y-m-d') ?? '-',
                    $t->completed ? 'Yes' : 'No',
                    $t->tags->pluck('name')->join(', ')
                ]);
            }
            fclose($f);
        };
        return response()->stream($callback, 200, $headers);
    }

public function exportPdf()
{
    $todos = Todo::with(['project','tags'])->get();
    $pdf   = Pdf::loadView('reports.pdf', compact('todos'));
    return $pdf->download('taskflow-report.pdf');
}
}
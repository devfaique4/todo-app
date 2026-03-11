<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Todo extends Model {
    use HasFactory;
    protected $fillable = [
        'project_id','title','description','priority','status',
        'category','due_date','due_time','completed','recurring','position'
    ];
    protected $casts = ['completed'=>'boolean','due_date'=>'date'];

    public function project()    { return $this->belongsTo(Project::class); }
public function tags()
{
    return $this->belongsToMany(Tag::class, 'todo_tag'); // ← table name specify karo
}
    public function subTasks()   { return $this->hasMany(SubTask::class)->orderBy('position'); }
    public function comments()   { return $this->hasMany(Comment::class)->latest(); }
    public function attachments(){ return $this->hasMany(Attachment::class); }
    public function history()    { return $this->hasMany(TodoHistory::class)->latest(); }

    public function isOverdue()  { return $this->due_date && $this->due_date->isPast() && !$this->completed; }
    public function isDueSoon()  { return $this->due_date && $this->due_date->isToday() && !$this->completed; }
    public function subTaskProgress() {
        $total = $this->subTasks->count();
        if (!$total) return 0;
        return round(($this->subTasks->where('completed',true)->count() / $total) * 100);
    }
}
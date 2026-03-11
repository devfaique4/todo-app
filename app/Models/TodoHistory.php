<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class TodoHistory extends Model {
    protected $fillable = ['todo_id','field_changed','old_value','new_value','action'];
    public function todo() { return $this->belongsTo(Todo::class); }
}
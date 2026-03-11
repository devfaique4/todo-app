<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model {
    protected $fillable = ['todo_id','body'];
    public function todo() { return $this->belongsTo(Todo::class); }
}
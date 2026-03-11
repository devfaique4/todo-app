<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class SubTask extends Model {
    protected $fillable = ['todo_id','title','completed','position'];
    protected $casts = ['completed'=>'boolean'];
    public function todo() { return $this->belongsTo(Todo::class); }
}
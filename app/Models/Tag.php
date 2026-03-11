<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = ['name', 'color'];

    public function todos()
    {
        return $this->belongsToMany(Todo::class, 'todo_tag'); // ← table name specify karo
    }
}
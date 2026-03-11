<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'todo_id',
        'title',
        'message',
        'type',
        'read',
    ];

    protected $casts = [
        'read' => 'boolean',
    ];

    public function todo()
    {
        return $this->belongsTo(Todo::class);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, Todo $todo)
    {
        $request->validate(['body' => 'required|string|max:2000']);
        $comment = $todo->comments()->create(['body' => $request->body]);
        return response()->json($comment);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(['ok' => true]);
    }
}

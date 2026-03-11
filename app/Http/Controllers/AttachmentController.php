<?php
namespace App\Http\Controllers;
use App\Models\{Todo, Attachment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; 

class AttachmentController extends Controller
{
    public function store(Request $request, Todo $todo)
    {
        $request->validate(['file'=>'required|file|max:10240']); // 10MB max
        $file = $request->file('file');
        $path = $file->store('attachments', 'public');
        $att  = $todo->attachments()->create([
            'filename'      => basename($path),
            'original_name' => $file->getClientOriginalName(),
            'mime_type'     => $file->getMimeType(),
            'size'          => $file->getSize(),
            'path'          => $path,
        ]);
        return response()->json($att);
    }
    public function download(Attachment $attachment)
    {
        return response()->download(storage_path('app/public/'.$attachment->path), $attachment->original_name);
    }
    public function destroy(Attachment $attachment)
    {
        Storage::disk('public')->delete($attachment->path);
        $attachment->delete();
        return response()->json(['ok'=>true]);
    }
}
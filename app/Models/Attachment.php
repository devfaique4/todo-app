<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model {
    protected $fillable = ['todo_id','filename','original_name','mime_type','size','path'];
    public function todo() { return $this->belongsTo(Todo::class); }
    public function sizeFormatted() {
        if ($this->size < 1024) return $this->size . ' B';
        if ($this->size < 1048576) return round($this->size/1024,1) . ' KB';
        return round($this->size/1048576,1) . ' MB';
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Project extends Model {
    protected $fillable = ['name','description','color','icon','status'];
    public function todos() { return $this->hasMany(Todo::class); }
    public function progress() {
        $total = $this->todos->count();
        if (!$total) return 0;
        return round(($this->todos->where('completed',true)->count() / $total) * 100);
    }
}
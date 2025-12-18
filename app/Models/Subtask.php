<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Subtask extends Model
{
    protected $fillable = ['task_id', 'content', 'is_done'];
    protected $casts = ['is_done' => 'boolean'];
    
    public function task() { return $this->belongsTo(Task::class); }
}
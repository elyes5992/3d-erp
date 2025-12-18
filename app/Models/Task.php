<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['content', 'is_done', 'due_date','priority'];
    protected $casts = [
        'is_done' => 'boolean',
        'due_date' => 'datetime',
    ];

     public function subtasks() 
    { 
        return $this->hasMany(Subtask::class); 
    }
}

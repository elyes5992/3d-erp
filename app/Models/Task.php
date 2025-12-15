<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['content', 'is_done', 'due_date'];
    protected $casts = [
        'is_done' => 'boolean',
    ];
}

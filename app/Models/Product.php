<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id', 'name', 'description', 'pinterest_url', 'status', 'remarks','cost'];
public function category() { return $this->belongsTo(Category::class); }
}

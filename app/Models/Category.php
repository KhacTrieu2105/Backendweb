<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',        // chỉ lưu tên file
        'parent_id',
        'sort_order',
        'description',
        'status',
        'created_by',
        'updated_by'
    ];
}

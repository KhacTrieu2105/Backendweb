<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductStore extends Model
{
    protected $table = 'product_stores';

    protected $fillable = [
        'product_id',
        'price_root',
        'qty',
        'created_by',
        'updated_by',
        'status',
    ];
}

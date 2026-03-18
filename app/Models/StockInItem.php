<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInItem extends Model
{
    protected $table = 'stock_in_items';

    public $timestamps = true;

    protected $fillable = [
        'stock_in_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function stockIn()
    {
        return $this->belongsTo(StockIn::class, 'stock_in_id', 'id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}

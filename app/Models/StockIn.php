<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $table = 'stock_insr'; // đúng theo DB hiện tại của bạn

    // 👉 BẬT timestamps cho chuẩn
    public $timestamps = true;

    protected $fillable = [
        'code',
        'supplier_id',
        'total_amount',
        'note',
        'status',
        'created_by',
        'updated_by',
    ];

    public function items()
    {
        return $this->hasMany(StockInItem::class, 'stock_in_id', 'id');
    }
}

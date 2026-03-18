<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'address',
        'note',
        'total_amount',   // Thêm dòng này
        'payment_method', // Thêm dòng này
        'status',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
    ];

    public $timestamps = false;

    // Sửa lại: Liên kết đến OrderDetail chứ không phải chính nó
    public function details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    protected $table = 'order_details';

    // THÊM DÒNG NÀY ĐỂ TẮT TỰ ĐỘNG CHÈN UPDATED_AT/CREATED_AT
    public $timestamps = false; 

    protected $fillable = [
        'order_id',
        'product_id',
        'qty',
        'price',
        'amount',   // Thêm nếu bạn muốn lưu tổng tiền từng dòng
        'discount'  // Thêm nếu bạn có lưu giảm giá
    ];

    // Liên kết ngược lại với đơn hàng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Liên kết với sản phẩm để lấy tên, hình ảnh sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
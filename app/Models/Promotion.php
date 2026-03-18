<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Promotion extends Model
{
    use HasFactory;

    protected $table = 'product_sales'; // nếu tên bảng đúng là "promotions"

    protected $fillable = [
        'name',
        'product_id',
        'price_sale',
        'date_begin',
        'date_end',
        'created_by',
        'updated_by',
        'status',
    ];

    // Cast các trường ngày giờ và kiểu dữ liệu phù hợp
    protected $casts = [
        'date_begin' => 'datetime',
        'date_end'   => 'datetime',
        'price_sale' => 'decimal:2',
        'status'     => 'boolean',
    ];

    // Quan hệ với sản phẩm (nếu có model Product)
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    // Quan hệ với người tạo (nếu dùng User)
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scope để lấy khuyến mãi đang hoạt động
    public function scopeActive($query)
    {
        return $query->where('status', 1)
                     ->where('date_begin', '<=', now())
                     ->where('date_end', '>=', now());
    }

}

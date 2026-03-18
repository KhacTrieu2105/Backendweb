<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    protected $table = 'product_attributes';
    public $timestamps = false; // Nếu bảng không có created_at, updated_at
    
    protected $fillable = [
        'product_id',
        'attribute_id',
        'value'
    ];

    // Quan hệ với bảng attributes
    public function attribute()
    {
        return $this->belongsTo(Attribute::class, 'attribute_id', 'id');
    }

    // Quan hệ với bảng products
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }
}
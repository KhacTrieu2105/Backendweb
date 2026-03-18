<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $table = 'product_images';

    protected $fillable = [
        'product_id',
        'image',
        'alt',
        'title'
    ];

    public $timestamps = false;

    protected $appends = ['image_url'];

    // ✅ Tạo full URL ảnh
    public function getImageUrlAttribute()
    {
        return $this->image
            ? asset('storage/' . $this->image) . '?t=' . ($this->product ? $this->product->updated_at->timestamp : time())
            : null;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

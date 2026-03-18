<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class Product extends Model
{
    protected $table = 'products';
    public $timestamps = true;

    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'thumbnail',
        'content',
        'description',
        'price_buy',
        'created_by',
        'updated_by',
        'status',
    ];

    // ✅ Quan hệ đến bảng PRODUCT_ATTRIBUTES (bảng trung gian)
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class, 'product_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id');
    }
      // 👇 QUAN TRỌNG
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image) . '?t=' . $this->updated_at->timestamp;
        }

        if ($this->thumbnail && Storage::disk('public')->exists($this->thumbnail)) {
            return asset('storage/' . $this->thumbnail) . '?t=' . $this->updated_at->timestamp;
        }

        return null;
    }
}

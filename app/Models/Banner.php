<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banners';
    protected $fillable = ['name', 'image', 'link', 'position', 'sort_order', 'description', 'status', 'created_by', 'updated_by'];

    // Tự động thêm image_url vào kết quả JSON
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            // Kiểm tra xem file có tồn tại không
            $imagePath = public_path('images/banner/' . $this->image);
            if (file_exists($imagePath)) {
                return asset('images/banner/' . $this->image);
            }
        }
        // Trả về data URL cho ảnh placeholder đơn giản
        return 'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="800" height="400" viewBox="0 0 800 400"><rect width="800" height="400" fill="#f0f0f0"/><text x="400" y="200" text-anchor="middle" font-family="Arial" font-size="24" fill="#999">Banner Placeholder</text></svg>');
    }
}

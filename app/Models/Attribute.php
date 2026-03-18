<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    protected $table = 'attributes';

    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    // Quan hệ đến bảng product_attributes
    public function productAttributes()
    {
        return $this->hasMany(ProductAttribute::class);
    }
}

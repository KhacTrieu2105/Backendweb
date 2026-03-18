<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'configs'; // ✅ đúng tên bảng

    protected $fillable = [
        'site_name',
        'email',
        'phone',
        'hotline',
        'address',
        'status',
    ];

    public $timestamps = false; // ⭐ QUAN TRỌNG NHẤT
}

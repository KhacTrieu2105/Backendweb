<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'users';

    protected $primaryKey = 'id';

    // Migration tự khai báo created_at và updated_at → phải tắt timestamps tự động
    public $timestamps = false;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'username',
        'password',
        'roles',
        'avatar',
        'address',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'status',
    ];

    /**
     * Ẩn các trường nhạy cảm khi trả về JSON
     */
    protected $hidden = [
        'password',
    ];
}

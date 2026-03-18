<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contacts';

    protected $fillable = [
        'user_id',
        'name',
        'email',
        'phone',
        'content',
        'reply_content',
        'reply_id',
        'created_by',
        'updated_by',
        'updated_at',
        'status',
    ];

    public $timestamps = false; // vì bạn đang tự quản lý created_at / updated_at
}

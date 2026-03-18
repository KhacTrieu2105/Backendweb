<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'topic_id',
        'title',
        'slug',
        'image',
        'content',
        'description',
        'post_type',
        'created_at',
        'created_by',
        'updated_at',
        'updated_by',
        'status',
    ];

    public $timestamps = false;

    public function topic()
    {
        return $this->belongsTo(Topic::class, 'topic_id');
    }

    // Image URL accessor
    protected $appends = ['image_url'];

    public function getImageUrlAttribute()
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return asset('storage/' . $this->image);
        }

        return null;
    }
}

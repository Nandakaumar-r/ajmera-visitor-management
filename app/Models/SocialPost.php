<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    use HasFactory;

    protected $fillable = [
        'content',
        'author_name',
        'author_avatar',
        'image',
        'likes_count',
        'comments_count',
        'platform', // linkedin, twitter, etc.
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
    ];

    // Scope for latest posts
    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope for most liked posts
    public function scopeMostLiked($query)
    {
        return $query->orderBy('likes_count', 'desc');
    }

    // Scope for most commented posts
    public function scopeMostCommented($query)
    {
        return $query->orderBy('comments_count', 'desc');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 
        'type', 
        'content', 
        'photo',
        'meta_title',
        'meta_description',
        'meta_image',
        'meta_url'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function hasMetaData()
    {
        return !empty($this->meta_url);
    }

    // Scope for most recent posts
    public function scopeMostRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    // Scope for trending posts (Example: based on likes count)
    public function scopeTrending($query)
    {
        return $query->withCount('likes')->orderBy('likes_count', 'desc');
    }

    public function comments() {
        return $this->hasMany(Comment::class);
    }
    
    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function likedBy(User $user) {
        return $this->likes()->where('user_id', $user->id)->exists();
    }
}

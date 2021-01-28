<?php

namespace App;

use App\Like;
use App\Comment;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = [
        'user_id', 'media_url', 'media_type', 'caption'
    ];

    public function comments() {
        return $this->hasMany(Comment::class);
    }

    public function therapist() {
        return $this->belongsTo(User::class,  'user_id', 'id');
    }

    public function likes() {
        return $this->hasMany(Like::class);
    }

    public function saves() {
        return $this->hasMany(Saved::class, 'post_id', 'id');
    }
    public function tags() {
        return $this->belongsToMany(Tags::class, 'posts_tags');
    }
}

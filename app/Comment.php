<?php

namespace App;

use App\Post;
use App\Subcomment;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id', 'post_id', 'comment',
    ];

    public function subcomments() {
        return $this->hasMany(Subcomment::class);
    }

    public function post() {
        return $this->belongsTo(Post::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}

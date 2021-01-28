<?php

namespace App;

use App\Post;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = [
        'user_id', 'post_id'
    ];

    public function post() {
        return $this->belongsTo(Post::class);
    }
}

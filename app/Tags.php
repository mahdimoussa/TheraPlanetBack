<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tags extends Model
{
    protected $fillable = [
        'label'
    ];

    public function posts() {
        return $this->belongsToMany(Post::class, 'posts_tags');
    }
}

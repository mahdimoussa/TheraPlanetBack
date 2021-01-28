<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Subcomment extends Model
{
    protected $fillable = [
        'user_id', 'comment_id', 'subcomment'
    ];

    public function comment() {
        return $this->belongsTo(Comment::class);
    }
    public function user() {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Saved extends Model
{
    protected $table = 'saved';

    protected $fillable = [
        'user_id', 'post_id'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}

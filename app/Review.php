<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'user_therapist_id', 'review', 'rating'
    ];

    public function therapist() {
        return $this->belongsTo(User::class);
    }
}

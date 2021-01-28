<?php

namespace App;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'user_id', 'id_photo', 'license_photo'
    ];

    public function therapist() {
        return $this->belongsTo(User::class);
    }
}

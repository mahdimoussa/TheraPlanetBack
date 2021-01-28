<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

use App\Document;
use App\Profile;
use App\Review;
use App\Saved;
use App\Post;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'type', 'phone_number'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function is($type)
    {
        return $this->type == $type;
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function ownsPost($post)
    {
        return $this->id == $post->user_id;
    }

    public function ownsComment($comment)
    {
        return $this->id == $comment->user_id;
    }

    public function ownsSubcomment($subcomment)
    {
        return $this->id == $subcomment->user_id;
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function saved_posts()
    {
        return $this->hasMany(Saved::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function document()
    {
        return $this->hasOne(Document::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

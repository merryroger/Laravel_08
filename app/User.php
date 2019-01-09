<?php

namespace App;

use App\Post;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function setLastPostActive()
    {
        $this->posts()
            ->where('status_id', 0)
            ->get()
            ->last()
            ->setActive();
    }

    public function deleteInactivePosts()
    {
        $this->posts()
            ->where('status_id', 0)
            ->get()
            ->filter(function ($item) {
                $item->delete();
            });
    }

}

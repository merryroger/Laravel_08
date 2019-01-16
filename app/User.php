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

    public function getPostsReversed($count)
    {
        return $this->posts()
            ->orderByDesc('id')
            ->skip(0)
            ->take($count)
            ->get();
    }

    public function getInactivePosts()
    {
        return $this->posts()->recordsByStatus(0);
    }

    public function getActivePosts()
    {
        return $this->posts()->recordsByStatus(1);
    }

    public function setLastPostActive()
    {
        $this->getInactivePosts()
            ->get()
            ->last()
            ->setActive();
    }

    public function deleteInactivePosts()
    {
        $this->getInactivePosts()
            ->get()
            ->map
            ->delete();
    }

}

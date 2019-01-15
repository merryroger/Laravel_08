<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Post extends Model
{
    protected $fillable = ['text', 'user_id', 'header', 'status_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeRecordsByStatus($query, $status)
    {
        return $query->where('status_id', $status);
    }

    public function isNotActive()
    {
        return $this->status_id === 0;
    }

    public function isActive()
    {
        return $this->status_id === 1;
    }

    public function setActive()
    {
        $this->update(['status_id' => 1]);
    }
}

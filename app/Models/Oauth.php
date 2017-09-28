<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Oauth extends Model
{
    protected $fillable = [
        'user_id',
        'oauth',
        'oauth_access_token',
        'oauth_expires'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

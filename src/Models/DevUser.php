<?php

namespace ZojaTech\DevGuard\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class DevUser extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password', 'role', 'email_verified_at'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
}

<?php

namespace ZojaTech\DevGuard\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class DevUser extends Authenticatable
{
    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];
}

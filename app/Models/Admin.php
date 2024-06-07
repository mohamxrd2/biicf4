<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'password',
        'phonenumber',
        'photo',
        'admin_type',
        'status',
        'last_seen',
        'isban'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'admin_id', 'id');
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];
}

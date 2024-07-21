<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class groupagefact extends Model
{
    use HasFactory;

    protected $table = 'groupagefact';

    protected $fillable = [
        'usersenders',
        'code_unique',
        'start_time',
        'notified',
    ];

    protected $casts = [
        'usersenders' => 'array',
        'start_time' => 'datetime',
        'notified' => 'boolean',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TontineUser extends Model
{
    use HasFactory;

    protected $table = 'tontine_user';

    protected $fillable = [
        'tontine_id',
        'user_id',
    ];
}

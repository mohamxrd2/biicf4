<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPromir extends Model
{
    use HasFactory;

    protected $table = 'user_promir';

    protected $fillable = [
        'nom',
        'prenom',
        'numero',
        'email',
    ];
}

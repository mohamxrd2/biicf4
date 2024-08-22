<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraisons extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experience',
        'vehicle',
        'vehicle2',
        'vehicle3',
        'zone',
        'continent',
        'sous_region', // Updated to snake_case
        'pays',
        'departe',
        'commune',
        'ville',
        'identity',
        'permis',
        'assurance',
        'etat',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

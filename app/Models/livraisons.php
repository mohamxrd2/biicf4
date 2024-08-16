<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class livraisons extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'experience',
        'license',
        'vehicle',
        'matricule',
        'availability',
        'continent',
        'Sous-Region',
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

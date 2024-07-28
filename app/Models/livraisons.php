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
        'zone',
        'comments',
        'identity',
        'permis',
        'assurance',
        'etat',
    ];

    protected $casts = [
        'zone' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

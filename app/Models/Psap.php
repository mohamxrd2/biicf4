<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Psap extends Model
{
    use HasFactory;

    protected $fillable = [
        'experience',
        'continent',
        'sous_region',
        'pays',
        'depart',
        'ville',
        'localite',
        'identity',
        'permis',
        'assurance',
        'user_id',
        'etat',
    ];

    /**
     * Get the user that owns the Psap.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

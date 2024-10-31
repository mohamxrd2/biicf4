<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'recu',
        'user_id',
        'statut',
    ];

    /**
     * Relation avec le modèle User
     * Un dépôt appartient à un utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

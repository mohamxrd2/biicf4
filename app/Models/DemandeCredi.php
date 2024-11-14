<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DemandeCredi extends Model
{
    use HasFactory;

    protected $table = 'demande_credi';

    protected $fillable = [
        'montant',
        'taux',
        'type_financement',
        'bailleur',
        'duree',
        'id_user',
        'demande_id',
        'objet_financement',
        'id_investisseur',
        'date_debut',
        'date_fin',
        'heure_debut',
        'heure_fin',
        'status',
        'count',
    ];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

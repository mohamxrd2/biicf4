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
        'id_investisseurs',
        'date_debut',
        'date_fin',
        'status',
        'count',
        'status',
    ];

    // Relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

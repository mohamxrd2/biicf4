<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class credits extends Model
{
    use HasFactory;

    protected $table = 'credits';

    protected $fillable = [
        'emprunteur_id',
        'investisseurs',
        'montant',
        'montant_restant',
        'taux_interet',
        'date_debut',
        'date_fin',
        'portion_journaliere',
        'statut',
    ];

    // Indiquer que le champ "investisseurs" est en JSON
    protected $casts = [
        'investisseurs' => 'array',
        'date_debut' => 'date',
        'date_fin' => 'date',
    ];

    /**
     * Relation avec l'utilisateur emprunteur.
     */
    public function emprunteur()
    {
        return $this->belongsTo(User::class, 'emprunteur_id');
    }



}

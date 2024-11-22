<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class projets_accordé extends Model
{
    use HasFactory;

    protected $table = 'projets_accordés';

    protected $fillable = [
        'emprunteur_id',
        'investisseurs',
        'montant',
        'montan_restantt',
        'action',
        'taux_interet',
        'date_debut',
        'date_fin',
        'portion_journaliere',
        'statut',
        'description',
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

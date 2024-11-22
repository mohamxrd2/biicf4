<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class credits_groupé extends Model
{
    use HasFactory;

    protected $table = 'credits_groupés';

    protected $fillable = [
        'emprunteur_id',
        'investisseurs',
        'montant',
        'montan_restantt',
        'taux_interet',
        'date_debut',
        'date_fin',
        'portion_journaliere',
        'comission',
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

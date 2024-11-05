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

    /**
     * Calculer la portion journalière en fonction du montant et de la durée.
     * @return float
     */
    public function calculerPortionJournaliere()
    {
        $jours = $this->date_debut->diffInDays($this->date_fin);
        return $jours > 0 ? $this->montant / $jours : 0;
    }

    /**
     * Vérifier si le crédit est actif en fonction de la date de fin.
     * @return bool
     */
    public function estActif()
    {
        return $this->statut === 'en_cours' && now()->lt($this->date_fin);
    }
}

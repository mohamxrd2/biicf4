<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class transactions_remboursement extends Model
{
    use HasFactory;
    protected $table = 'transactions';

    protected $fillable = [
        'credit_id',
        'emprunteur_id',
        'investisseur_id',
        'montant',
        'interet',
        'date_transaction',
        'statut',
    ];

    protected $casts = [
        'date_transaction' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }

    /**
     * Relation avec l'emprunteur (utilisateur).
     */
    public function emprunteur()
    {
        return $this->belongsTo(User::class, 'emprunteur_id');
    }

    /**
     * Relation avec l'investisseur (utilisateur).
     */
    public function investisseur()
    {
        return $this->belongsTo(User::class, 'investisseur_id');
    }

    /**
     * Vérifie si la transaction est réussie.
     *
     * @return bool
     */
    public function estEffectue()
    {
        return $this->statut === 'effectue';
    }

    /**
     * Calcule le total de la transaction incluant le montant et l'intérêt.
     *
     * @return float
     */
    public function montantTotal()
    {
        return $this->montant + $this->interet;
    }
}

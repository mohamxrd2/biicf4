<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class remboursements extends Model
{
    use HasFactory;

    protected $table = 'remboursements';

    protected $fillable = [
        'credit_id',
        'montant_capital',
        'montant_interet',
        'date_remboursement',
        'statut',
    ];

    protected $casts = [
        'date_remboursement' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }

    /**
     * Calcul du montant total remboursé (capital + intérêt).
     *
     * @return float
     */
    public function montantTotalRembourse()
    {
        return $this->montant_capital + $this->montant_interet;
    }

    /**
     * Vérifier si le remboursement est en attente.
     *
     * @return bool
     */
    public function estEnAttente()
    {
        return $this->statut === 'en_attente';
    }

    /**
     * Vérifier si le remboursement a été effectué.
     *
     * @return bool
     */
    public function estEffectue()
    {
        return $this->statut === 'effectue';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class portions_journalieres extends Model
{
    use HasFactory;

    protected $table = 'credit_portions';

    protected $fillable = [
        'credit_id',
        'date_portion',
        'portion_capital',
        'portion_interet',
    ];

    protected $casts = [
        'date_portion' => 'date',
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }

    /**
     * Calcul du montant total de la portion journalière (capital + intérêt).
     *
     * @return float
     */
    public function montantTotalPortion()
    {
        return $this->portion_capital + $this->portion_interet;
    }

    /**
     * Vérifier si la portion est due aujourd'hui.
     *
     * @return bool
     */
    public function estDueAujourdHui()
    {
        return $this->date_portion->isToday();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rapports extends Model
{
    use HasFactory;


    protected $table = 'rapports'; // Spécifiez le nom de la table si elle ne suit pas la convention

    protected $fillable = [
        'credit_id',
        'transaction_id',
        'contenu',
        'type',
        'date_rapport',
    ];

    protected $casts = [
        'date_rapport' => 'date', // Cast pour assurer que la date est traitée comme une date
    ];

    /**
     * Relation avec le crédit concerné.
     */
    public function credit()
    {
        return $this->belongsTo(credits::class, 'credit_id');
    }

    /**
     * Relation avec la transaction concernée.
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }

    /**
     * Vérifie si le rapport est lié à un crédit.
     *
     * @return bool
     */
    public function estLieAuCredit()
    {
        return !is_null($this->credit_id);
    }

    /**
     * Vérifie si le rapport est lié à une transaction.
     *
     * @return bool
     */
    public function estLieALaTransaction()
    {
        return !is_null($this->transaction_id);
    }
}

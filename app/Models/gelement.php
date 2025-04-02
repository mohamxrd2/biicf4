<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gelement extends Model
{
    use HasFactory;

    // Nom de la table associé au modèle
    protected $table = 'gelements';

    // Les attributs qui peuvent être assignés en masse
    protected $fillable = [
        'id_wallet',
        'amount',
        'reference_id',
        'status',
    ];

    /**
     * Relation avec le modèle Wallet.
     * Un FundHolding appartient à un Wallet.
     */
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'id_wallet');
    }
}

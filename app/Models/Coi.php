<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coi extends Model
{
    use HasFactory;

    protected $table = 'coi';

    protected $fillable = [
        'Solde',
        'type_compte',
        'Date_Creation',
        'id_wallet',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    // Relation avec le modèle Wallet
    public function wallet()
    {
        return $this->belongsTo(Wallet::class, 'id_wallet');
    }
}

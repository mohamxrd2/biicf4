<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotisation extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'tontine_id', 'montant', 'statut'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tontine()
    {
        return $this->belongsTo(Tontine::class);
    }

    public function echecPaiement()
    {
        return $this->hasOne(EchecPaiement::class);
    }
}

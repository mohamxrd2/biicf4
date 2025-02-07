<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tontine extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'montant_cotisation', 'frequence', 'date_fin', 'frais_gestion'
    ];

    public function cotisations() {
        return $this->hasMany(Cotisation::class);
    }

    public function transactions() {
        return $this->hasMany(Transaction::class);
    }
}

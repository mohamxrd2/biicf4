<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tontines extends Model
{
    use HasFactory;

    protected $fillable = [
        'isUnlimited',
        'gelement_reference',
        'date_debut',
        'montant_cotisation',
        'frequence',
        'date_fin',
        'frais_gestion',
        'next_payment_date',
        'gain_potentiel',
        'nombre_cotisations',
        'user_id',
        'statut',
    ];

    // Also add this if you're using boolean casting
    protected $casts = [
        'isUnlimited' => 'boolean',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class, 'tontine_user', 'tontine_id', 'user_id')
            ->withTimestamps();
    }

    public function cotisations()
    {
        return $this->hasMany(Cotisation::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}

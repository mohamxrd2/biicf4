<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EchecPaiement extends Model
{
    use HasFactory;

    protected $table = 'echecs_paiement';
    protected $fillable = ['user_id', 'cotisation_id', 'montant_du'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cotisation()
    {
        return $this->belongsTo(Cotisation::class, 'cotisation_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EchecPaiement extends Model
{
    use HasFactory;


    protected $fillable = ['user_id', 'cotisation_id', 'montant_du'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function cotisation() {
        return $this->belongsTo(Cotisation::class);
    }
}

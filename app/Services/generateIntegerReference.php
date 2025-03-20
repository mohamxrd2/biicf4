<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;

class generateIntegerReference
{
    public function generate(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

}

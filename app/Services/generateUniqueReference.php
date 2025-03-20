<?php

namespace App\Services;

use App\Models\Transaction;
use Illuminate\Support\Str;

class generateUniqueReference
{
    public function generate()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

}

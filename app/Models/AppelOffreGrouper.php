<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffreGrouper extends Model
{
    use HasFactory;

    protected $table = 'appeloffregrouper';

    protected $fillable = [
        'productName',
        'quantity',
        'payment',
        'Livraison',
        'dateTot',
        'dateTard',
        'specificity',
        'id_prod',
        'image',
        'prodUsers',
        'codeunique' // Add the new column here
    ];

    // Optionally, if you want prodUsers to be cast to an array automatically
    protected $casts = [
        'prodUsers' => 'array',
    ];
}

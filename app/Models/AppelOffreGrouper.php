<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppelOffreGrouper extends Model
{
    use HasFactory;

    protected $table = 'appeloffregrouper';

    protected $fillable = [
        'lowestPricedProduct',
        'productName',
        'quantity',
        'payment',
        'Livraison',
        'dateTot',
        'dateTard',
        'specificity',
        'localite',
        'id_prod',
        'image',
        'prodUsers',
        'codeunique', // Add the new column here
        'user_id'
    ];
    public function User()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Optionally, if you want prodUsers to be cast to an array automatically
    protected $casts = [
        'prodUsers' => 'array',
    ];
}

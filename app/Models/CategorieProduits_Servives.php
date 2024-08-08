<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorieProduits_Servives extends Model
{
    use HasFactory;

    protected $table = 'categorie_produits__servives';

    protected $fillable = [
        'categorie_produit_services',
    ];

    public function produitservice()
    {
        return $this->hasMany(ProduitService::class, 'categorie_id');
    }
}

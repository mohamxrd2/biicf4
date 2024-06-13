<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    // Définir le nom de la table explicitement si ce n'est pas la forme plurielle du nom du modèle
    protected $table = 'comments';

    // Définir les attributs qui sont assignables
    protected $fillable = [
        'prixTrade',
        'id_trader',
        'code_unique',
        'id_prod',
    ];

    /**
     * Get the user that owns the comment.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'id_trader');
    }

    /**
     * Get the product associated with the comment.
     */
    public function produit()
    {
        return $this->belongsTo(ProduitService::class, 'id_prod');
    }
}

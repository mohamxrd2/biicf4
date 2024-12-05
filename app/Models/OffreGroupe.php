<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OffreGroupe extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'offre_groupe';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'quantite',
        'code_unique',
        'zone',
        'produit_id',
        'count',
        'user_id',
        'notified',
    ];

    /**
     * Get the produit that owns the OffreGroupe.
     */
    public function produit()
    {
        return $this->belongsTo(ProduitService::class, 'produit_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

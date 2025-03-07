<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitService extends Model
{
    use HasFactory;
    protected $table = 'produit_services'; // Nom de la table correspondant

    protected $fillable = [
        'name',
        'reference',
        'type',

        //produit
        'condProd',
        'formatProd',
        'qteProd_min',
        'qteProd_max',
        'specification',
        'specification2',
        'specification3',
        'origine',
        'Particularite',

        //
        'prix',
        'photoProd1',
        'photoProd2',
        'photoProd3',
        'photoProd4',
        'videoProd',
        //

        //service
        'description',
        'specialite',
        'experience',
        'duree',
        'disponible',
        'lieu',

        //
        'user_id',
        'statuts',
        'categorie_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function achatsDirects()
    {
        return $this->hasMany(AchatDirect::class, 'idProd');
    }
    public function categorie()
    {
        return $this->belongsTo(CategorieProduits_Servives::class, 'categorie_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'type',
        'condProd',
        'formatProd',
        'qteProd_min',
        'qteProd_max',
        'specification',
        'specification2',
        'specification3',
        'origine',
        //
        'prix',
        //
        'LivreCapProd',
        'photoProd1',
        'photoProd2',
        'photoProd3',
        'photoProd4',
        'videoProd',
        'Particularite',
        'qalifServ',
        'sepServ',
        'qteServ',
        'zonecoServ',
        'villeServ',
        'comnServ',
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

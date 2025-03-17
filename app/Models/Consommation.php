<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consommation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'reference',
        'type',
        'conditionnement',
        'format',
        'Particularite',
        'origine',
        'periodicite',
        'qte',
        'description',
        'prix',
        'frqce_cons',
        'jourAch_cons',
        'qualif_serv',
        'specialité',
        'description',
        'zoneAct',
        'villeCons',
        'id_user',
        'statuts',
        'categorie_id',

    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function categorie()
    {
        return $this->belongsTo(CategorieProduits_Servives::class, 'categorie_id');
    }
}

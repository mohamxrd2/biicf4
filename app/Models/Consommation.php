<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Consommation extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'conditionnement',
        'format',
        'qte',
        'prix',
        'frqce_cons',
        'jourAch_cons',
        'qualif_serv',
        'specialité',
        'description',
        'zoneAct',
        'villeCons',
        'id_user',
        'statuts'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

}

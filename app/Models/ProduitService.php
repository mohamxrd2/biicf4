<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduitService extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'condProd',
        'formatProd',
        'qteProd_min',
        'qteProd_max',
        'prix',
        'LivreCapProd',
        'photoProd1',
        'photoProd2',
        'photoProd3',
        'photoProd4',
        'videoProd',
        'desrip',
        'qalifServ',
        'sepServ',
        'qteServ',
        'zonecoServ',
        'villeServ',
        'comnServ',
        'user_id',
        'statuts'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function achatsDirects()
    {
        return $this->hasMany(AchatDirect::class, 'idProd');
    }
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtolower($value); // Convertir le nom en minuscules
    }
}

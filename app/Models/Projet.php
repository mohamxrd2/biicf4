<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Projet extends Model
{
    use HasFactory;

    protected $table = 'projet';

    protected $fillable = [
        'montant',
        'taux',
        'description',
        'categorie',
        'type_financement',
        'statut',
        'id_user',
        'photo1', // Si vous avez ajouté ces colonnes
        'photo2',
        'photo3',
        'photo4',
        'photo5',
        'durer'
    ];


    /**
     * Relation avec le demandeur (User)
     */
    public function demandeur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function ajoutsMontants()
    {
        return $this->hasMany(AjoutMontant::class, 'id_projet');
    }
}

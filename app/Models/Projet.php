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
        'durer',
        'date_fin',
        'name',
        'count',
        'Portion_action',
        'Portion_obligt',
        'nombreActions',
        'etat',
        'bailleur', // Nouveau champ pour le bailleur
        'id_investisseur', // Nouveau champ pour les investisseurs
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
    //  // Méthode pour récupérer les photos
    //  public function getPhotosAttribute()
    //  {
    //      return collect([$this->photo1, $this->photo2, $this->photo3, $this->photo4, $this->photo5])
    //          ->filter() // Filtrer les valeurs nulles
    //          ->map(function ($photo) {
    //              return asset('storage/' . $photo); // Générer l'URL absolue pour les photos
    //          });
    //  }
}

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
    ];

    /**
     * Relation avec le demandeur (User)
     */
    public function demandeur()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjoutMontant extends Model
{
    use HasFactory;

    protected $table = 'ajout_montant';

    protected $fillable = [
        'montant',
        'id_invest',
        'id_emp',
        'id_projet',
        'id_demnd_credit',
    ];

    /**
     * Relation avec l'investisseur (User)
     */
    public function investisseur()
    {
        return $this->belongsTo(User::class, 'id_invest');
    }

    /**
     * Relation avec l'emprunteur (User)
     */
    public function emprunteur()
    {
        return $this->belongsTo(User::class, 'id_emp');
    }
    public function projet()
    {
        return $this->belongsTo(Projet::class, 'id_projet');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AjoutAction extends Model
{
    use HasFactory;

    protected $table = 'ajout_action';

    protected $fillable = [
        'nombreActions',
        'montant',
        'id_invest',
        'id_emp',
        'id_projet',
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

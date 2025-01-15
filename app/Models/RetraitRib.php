<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RetraitRib extends Model
{
    use HasFactory;


    // Définir la table associée (optionnel si la table porte le nom au pluriel et suit la convention)
    protected $table = 'retrait_rib';

    // Définir les colonnes pouvant être remplies (fillable)
    protected $fillable = [
        'rib',
        'amount',
        'id_user',
        'status',
        'reference',
        'code1',  // Ajout de code1
        'code2',
        'cle_iban',
        'code_bic',
        'code_bank',
        'code_guiche',
        'numero_compte',
        'iban',
        'bank_name'
    ];

    // Définir la relation avec le modèle User
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    // Si vous souhaitez un comportement particulier pour les dates (par exemple, les dates en format Carbon)
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RechargeSos extends Model
{
    use HasFactory;

    // Spécifiez le nom de la table si nécessaire
    protected $table = 'rechargesos'; // Utilisez 'rechargesos' si vous n'avez pas changé le nom dans la migration

    // Spécifiez les attributs que vous pouvez remplir
    protected $fillable = [
        'userdem',      // ID de l'utilisateur qui demande la recharge
        'userinvest',   // ID de l'utilisateur qui investit
        'montant',      // Montant de la recharge
        'roi',          // Retour sur investissement
        'operator',     // Opérateur de recharge
        'phone',        // Numéro de téléphone
        'statut',       // Statut de la demande
        'id_sos'        // Identifiant SOS
    ];

    // Définir les relations avec le modèle User
    public function userDemandeur()
    {
        return $this->belongsTo(User::class, 'userdem');
    }

    public function userInvestisseur()
    {
        return $this->belongsTo(User::class, 'userinvest');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Countdown extends Model
{
    use HasFactory;

    // Définition des champs remplissables dans le modèle
    protected $fillable = [
        'user_id',
        'userSender',
        'start_time',
        'end_time',
        'time_remaining',
        'is_active',
        'notified',
        'difference',
        'code_unique',
        'id_achat',
        'id_appeloffre',
        'AppelOffreGrouper_id',
    ];
    protected $casts = [
        'end_time' => 'datetime',
        'is_active' => 'boolean',
    ];
    // Méthodes relationnelles
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'userSender');
    }

    public function achat()
    {
        return $this->belongsTo(AchatDirect::class, 'id_achat');
    }

    public function appelOffre()
    {
        return $this->belongsTo(AppelOffreUser::class, 'id_appeloffre'); // Correction du modèle appelé
    }

    public function appelOffreGrouper()
    {
        return $this->belongsTo(AppelOffreGrouper::class, 'AppelOffreGrouper_id'); // Nouvelle méthode relationnelle ajoutée
    }
}

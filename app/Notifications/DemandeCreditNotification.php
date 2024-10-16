<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeCreditNotification extends Notification
{
    use Queueable;

    protected $demande;

    public function __construct($demande)
    {
        $this->demande = $demande; // Stocke les informations de la demande
    }

    public function via($notifiable)
    {
        return ['database']; // Choisir les canaux de notification (base de données et mail)
    }

    public function toArray($notifiable)
    {
        return [
            'demande_id' => $this->demande['demande_id'] ?? null, // Accéder aux clés du tableau
            'montant' => $this->demande['montant'] ?? null,
            'duree' => $this->demande['duree'] ?? null,
            'type_financement' => $this->demande['type_financement'] ?? null,
            'bailleur' => $this->demande['bailleur'] ?? null,
            'user_id' => $this->demande['user_id'] ?? $this->demande['id_user'] ?? null,
            'id_investisseur' => $this->demande['id_investisseur'] ?? null,
        ];
    }
}

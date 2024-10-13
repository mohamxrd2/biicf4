<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeCreditProjetNotification extends Notification
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
            'projet_id' => $this->demande['id_projet'],  // Utilisation de tableau pour le $demande
            'montant' => $this->demande['montant'],
            'duree' => $this->demande['duree'],
            'type_financement' => $this->demande['type_financement'],
            'user_id' => $this->demande['user_id'],
            'id_investisseur' => $this->demande['id_investisseur'],
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CountdownNotificationAp extends Notification implements ShouldQueue
{
    use Queueable;

    public $details;

    public function __construct($details)
    {
        $this->details = $details;
    }

    // Spécifie les canaux de notification
    public function via($notifiable)
    {
        return ['database']; // Notification stockée dans la base de données
    }

    // Formate les données pour la base de données
    public function toDatabase($notifiable)
    {
        return [
            'code_unique' => $this->details['code_unique'] ?? null,
            'prixTrade' => $this->details['prixTrade'] ?? null,
            'livreur' => $this->details['livreur'] ?? null,
            'id_appeloffre' => $this->details['id_appeloffre'] ?? null,
            'prixFin' => $this->details['prixFin'] ?? null,

        ];
    }
}

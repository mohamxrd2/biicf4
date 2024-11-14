<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GagnantProjetNotifications extends Notification implements ShouldQueue
{
    use Queueable;

    private $details;

    /**
     * Create a new notification instance.
     */
    public function __construct($details)
    {
        $this->details = $details;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'taux' => $this->details['taux'] ?? null, // Vérifie que la clé 'taux' existe dans le tableau
            'id_invest' => $this->details['id_invest'] ?? null, // Vérifie que la clé 'id_invest' existe
            'id_emp' => $this->details['id_emp'] ?? null, // Vérifie que la clé 'id_emp' existe
            'credit_id' => $this->details['credit_id'] ?? null, // Vérifie que la clé 'id_projet' existe
            'duree' => $this->details['duree'] ?? null, // Vérifie que la clé 'id_projet' existe
            'montant' => $this->details['montant'] ?? null, // Vérifie que la clé 'id_projet' existe
            'type_financement' => $this->details['type_financement'] ?? null, // Vérifie que la clé 'id_projet' existe
        ];
    }
}

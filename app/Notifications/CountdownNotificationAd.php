<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class CountdownNotificationAd extends Notification implements ShouldQueue
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
            'achat_id' => $this->details['id'] ?? null,
            'prixFin' => $this->details['prixFin'] ?? null,
            'type_achat' => $this->details['type_achat'] ?? null,
            'title' => 'Facture Proformat',
            'description' => 'Cliquez pour voir plus de détails.',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 11.625h4.5m-4.5 2.25h4.5m2.121 1.527c-1.171 1.464-3.07 1.464-4.242 0-1.172-1.465-1.172-3.84 0-5.304 1.171-1.464 3.07-1.464 4.242 0M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
              </svg>',

        ];
    }
}

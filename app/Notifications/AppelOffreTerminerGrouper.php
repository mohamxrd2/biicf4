<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class AppelOffreTerminerGrouper extends Notification implements ShouldQueue
{
    use Queueable;

    public $details;
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
        Log::info('IDs to notify:', $this->details);

        return [
            'code_unique' => $this->details['code_unique'] ?? null,
            'prixTrade' => $this->details['prixTrade'] ?? null,
            'achat_id' => $this->details['achat_id'] ?? null,
            'id_appeloffre' => $this->details['id_appeloffre'] ?? null,
            'title' => 'Gagnant de negociation',
            'description' => 'Cliquez pour voir les details de la commande',

        ];
    }
}

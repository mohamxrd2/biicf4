<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppelOffreTerminer extends Notification implements ShouldQueue
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
        return [
            'code_unique' => $this->details['code_unique'] ?? null,
            'prixTrade' => $this->details['prixTrade'] ?? null,
            'id_trader' => $this->details['id_trader'] ?? null,
            'quantiteC' => $this->details['quantiteC'] ?? null,
            'localite' => $this->details['localite'] ?? null,
            'specificite' => $this->details['specificite'] ?? null,
            'nameprod' => $this->details['nameprod'] ?? null,
            'id_sender' => $this->details['id_sender'] ?? null,
            'montantTotal' => $this->details['montantTotal'] ?? null,
        ];
    }
}

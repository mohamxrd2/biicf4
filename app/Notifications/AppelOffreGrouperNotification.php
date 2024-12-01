<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AppelOffreGrouperNotification extends Notification implements ShouldQueue
{
    use Queueable;

    private $achat;

    public function __construct($achat)
    {
        $this->achat = $achat;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'dateTot' => $this->achat['dateTot'] ?? null,
            'dateTard' => $this->achat['dateTard'] ?? null,
            'productName' => $this->achat['productName'] ?? null,
            'totalPersonnestotalPersonnes' => $this->achat['totalPersonnes'] ?? null,
            'code_unique' => $this->achat['code_unique'],
            'quantiteTotale' => $this->achat['quantiteTotale'] ?? null,
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchatGroupBiicf extends Notification implements ShouldQueue
{
    use Queueable;

    private $achat;

    /**
     * Create a new notification instance.
     */
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
            'nameProd' => $this->achat['nameProd'],
            'quantité' => $this->achat['quantité'],
            'montantTotal' => $this->achat['montantTotal'],
            'localite' => $this->achat['localite'] ?? null,
            'specificite' => $this->achat['specificite'] ?? null,
            'userTrader' => $this->achat['userTrader'],
            'userSender' => $this->achat['userSender'] ?? null,
            'photoProd' => $this->achat['photoProd'],
            'idProd' => $this->achat['idProd'],
        ];
    }
}

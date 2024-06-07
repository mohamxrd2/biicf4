<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AchatBiicf extends Notification implements ShouldQueue
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
            'localite' => $this->achat['localite'],
            'specificite' => $this->achat['specificite'],
            'userTrader' => $this->achat['userTrader'],
            'userSender' => $this->achat['userSender'],
            'photoProd' => $this->achat['photoProd'],
            'idProd' => $this->achat['idProd'],
        ];
    }
}

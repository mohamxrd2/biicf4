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
            'quantity' => $this->achat['quantity'] ?? null,
            'payment' => $this->achat['payment'] ?? null,
            'Livraison' => $this->achat['Livraison'] ?? null,
            'specificity' => $this->achat['specificity'] ?? null,
            'localite' => $this->achat['localite'] ?? null,
            'image' => $this->achat['image'] ?? null,
            'id_sender' => $this->achat['id_sender'] ?? null,
            'prodUsers' => $this->achat['prodUsers'] ?? null,
            'lowestPricedProduct' => $this->achat['lowestPricedProduct'] ?? null,
            'code_unique' => $this->achat['code_unique'],
            'difference' => $this->achat['difference'] ?? null,
        ];
    }
}

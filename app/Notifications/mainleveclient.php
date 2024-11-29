<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class mainleveclient extends Notification
{
    use Queueable;
    private $main;
    /**
     * Create a new notification instance.
     */
    public function __construct($main)
    {
        $this->main = $main;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'code_unique' => $this->main['code_unique'],
            'fournisseur' => $this->main['fournisseur'],
            'livreur' => $this->main['livreur'],
            'prixTrade' => $this->main['prixTrade'],
            'date_livr' => $this->main['date_livr'],
            'time' => $this->main['time'],
            'achat_id' => $this->main['achat_id'] ?? null,
            'id_appeloffre' => $this->main['id_appeloffre'] ?? null,
            'CodeVerification' => $this->main['CodeVerification'],
            'title' => $this->main['title'],
            'description' => $this->main['description'],

        ];
    }
}

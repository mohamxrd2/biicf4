<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class mainleveAp extends Notification
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
            'fournisseur' => $this->main['fournisseur'] ?? null,
            'livreur' => $this->main['livreur'] ?? null,
            'fournisseurCode' => $this->main['fournisseurCode'] ?? null,
            'livreurCode' => $this->main['livreurCode'] ?? null,
            'client' => $this->main['client'],
            'id_appeloffre' => $this->main['id_appeloffre'] ?? null,
            'prixTrade' => $this->main['prixTrade'] ?? null,
            'title' => $this->main['title'],
            'description' => $this->main['description'],

        ];
    }
}

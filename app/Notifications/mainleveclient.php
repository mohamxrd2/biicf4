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
           'idProd' => $this->main['idProd'],
            'code_unique' =>$this->main['code_unique'],
            'fournisseur' => $this->main['fournisseur'],
            'quantite' => $this->main['quantite'],
            'localité' => $this->main['localité'],
            'id_client' => $this->main['id_client'],
            'livreur' => $this->main['livreur'],
            'date_livr' => $this->main['date_livr'],
            'matine' => $this->main['matine'],
            'prixTrade' => $this->main['prixTrade'],
            'prixProd' => $this->main['prixProd']
            
        ];
    }
}

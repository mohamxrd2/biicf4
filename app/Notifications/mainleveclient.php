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
            'id_trader' => $this->main['id_trader'],
            'quantite' => $this->main['quantite'],
            'localité' => $this->main['localité'],
            'id_client' => $this->main['id_client'],
            'id_livreur' => $this->main['id_livreur'],
            'date_livr' => $this->main['date_livr'],
            'matine' => $this->main['matine']
            
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;


class OffreNegosDone extends Notification
{
    use Queueable;

    protected $offredone;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $offredone)
    {
        $this->offredone = $offredone;
      
    }


    public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        return [
            'produit_id' => $this->offredone['produit_id'],
            'produit_name' => $this->offredone['produit_name'],
            'quantite' => $this->offredone['quantite'],
            'code_unique' => $this->offredone['code_unique'],
          
        ];
    }
}

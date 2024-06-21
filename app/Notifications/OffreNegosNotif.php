<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ProduitService;

class OffreNegosNotif extends Notification
{
    use Queueable;

    protected $offrenegos;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $offrenegos)
    {
        $this->offrenegos = $offrenegos;
      
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
            'produit_id' => $this->offrenegos['produit_id'],
            'produit_name' => $this->offrenegos['produit_name'],
            'quantite' => $this->offrenegos['quantite'],
            'code_unique' => $this->offrenegos['code_unique']
        ];
    }
}

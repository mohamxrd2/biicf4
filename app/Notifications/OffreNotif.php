<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ProduitService;

class OffreNotif extends Notification
{
    use Queueable;

    protected $produit;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $produit)
    {
        $this->produit = $produit;
    }


    public function via($notifiable)
    {
        return ['database']; // Vous pouvez ajouter d'autres canaux de notification ici, comme 'mail', 'broadcast', etc.
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
            'produit_id' => $this->produit->id,
            'produit_name' => $this->produit->name,
            'message' => 'Nouvelle offre pour le produit: ' . $this->produit->name,
        ];
    }
}

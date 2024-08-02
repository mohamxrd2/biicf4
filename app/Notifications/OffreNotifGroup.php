<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\ProduitService;

class OffreNotifGroup extends Notification
{
    use Queueable;

    protected $produit;
    protected $Uniquecode;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $produit, $Uniquecode)
    {
        $this->produit = $produit;
        $this->Uniquecode  = $Uniquecode;
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
            'produit_id' => $this->produit->id,
            'produit_name' => $this->produit->name,
            'produit_conditionnement' => $this->produit->condProd,
            'produit_livraison' => $this->produit->LivreCapProd,
            'produit_prix' => $this->produit->prix,
            'Uniquecode' => $this->Uniquecode,
            'message' => 'Nouvelle offre pour le produit: ' . $this->produit->name,
        ];
    }
}

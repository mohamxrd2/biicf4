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
    protected $code_unique;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct( $produit, $code_unique)
    {
        $this->produit = $produit;
        $this->code_unique  = $code_unique;
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
            'idProd' => $this->produit->id,
            'produit_name' => $this->produit->name,
            'produit_prix' => $this->produit->prix,
            'code_unique' => $this->code_unique,
            'title' => 'Enchere',
            'description' => 'Cliquez pour participer a l\'enchere',
        ];
    }
}

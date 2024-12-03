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
    public function __construct($produit, $code_unique)
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
            'code_unique' => $this->code_unique,
            'title' => 'Enchere',
            'description' => "Cliquez pour participer à l'enchère",
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18 9 11.25l4.306 4.306a11.95 11.95 0 0 1 5.814-5.518l2.74-1.22m0 0-5.94-2.281m5.94 2.28-2.28 5.941" />
            </svg>',
        ];
    }
}

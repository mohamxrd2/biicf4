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
    public function __construct($offrenegos)
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
            'idProd' => $this->offrenegos['idProd'],
            'produit_name' => $this->offrenegos['produit_name'],
            'quantite' => $this->offrenegos['quantite'],
            'code_unique' => $this->offrenegos['code_unique'],

            'title' => 'Groupage de fournisseurs',
            'description' => 'le groupage vient de commencer. Cliquez pour participer.',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
          </svg>
          ',
        ];
    }
}

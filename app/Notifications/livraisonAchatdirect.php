<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class livraisonAchatdirect extends Notification
{
    use Queueable;
    private $livraison;
    /**
     * Create a new notification instance.
     */
    public function __construct($livraison)
    {
        $this->livraison = $livraison;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'idProd' => $this->livraison['idProd'],
            'code_unique' => $this->livraison['code_livr'],
            'textareaContent' => $this->livraison['textareaContent'],
            'photoProd' => $this->livraison['photoProd'],
            'achat_id' => $this->livraison['achat_id'],
            'title' => 'Négociation des livreurs',
            'description' => 'Cliquez pour participer a la negociation.',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21 3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
          </svg>',
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class OffreNegosDone extends Notification
{
    use Queueable;

    protected $offredone;

    /**
     * Create a new notification instance.
     *
     * @param array $offredone
     * @return void
     */
    public function __construct(array $offredone)
    {
        $this->offredone = $offredone;
    }

    /**
     * Determine the channels the notification will be sent on.
     *
     * @param mixed $notifiable
     * @return array
     */
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
        Log::info('Notification data: ', $this->offredone); // Log the data being sent

        return [
            'quantite_totale' => $this->offredone['quantite_totale'],
            'details_par_user' => $this->offredone['details_par_user'],
            'idProd' => $this->offredone['idProd'],
            'id_sender' => $this->offredone['id_sender'],
            'code_unique' => $this->offredone['code_unique'],
            'title' => 'Groupage de fournisseurs terminé',
            'description' => 'Cliquez pour voir les details du groupage.',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m3.75 9v6m3-3H9m1.5-12H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
          </svg>
          ',
        ];
    }
}

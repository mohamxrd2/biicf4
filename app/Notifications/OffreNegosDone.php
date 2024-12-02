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
            'title' => 'Groupage de fournisseurs',
            'description' => 'Cliquez pour voir les details du groupage.',
        ];
    }
}

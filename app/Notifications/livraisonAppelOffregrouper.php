<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class livraisonAppelOffregrouper extends Notification
{
    use Queueable;
    private $livraisonVerif;
    /**
     * Create a new notification instance.
     */
    public function __construct($livraisonVerif)
    {
        $this->livraisonVerif = $livraisonVerif;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'idProd' => $this->livraisonVerif['idProd'],
            'quantite' => $this->livraisonVerif['quantite'],
            'id_trader' => $this->livraisonVerif['id_trader'],
            'totalSom' => $this->livraisonVerif['totalSom'],
            'localite' => $this->livraisonVerif['localite'],
            'userSender' => $this->livraisonVerif['userSender'],
            'code_livr' => $this->livraisonVerif['code_livr'],
            'code_unique' => $this->livraisonVerif['code_unique'],
            'prixProd' => $this->livraisonVerif['prixProd'],
            'textareaContent' => $this->livraisonVerif['textareaContent'],
            'dateTot' => $this->livraisonVerif['dateTot'],
            'dateTard' => $this->livraisonVerif['dateTard'],
            'type_achat' => $this->livraisonVerif['type_achat'] ?? null, // Ajout de type d'achat
            'user_id' => $this->livraisonVerif['user_id'] ?? null, // Ajout de l'ID utilisateur
        ];
    }
}

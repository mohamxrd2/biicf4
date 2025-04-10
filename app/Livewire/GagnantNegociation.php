<?php

namespace App\Livewire;

use Livewire\Component;

class GagnantNegociation extends Component
{
    public $notifications = [];
    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications->filter(function ($notification) {
            // Filtrer les notifications par plusieurs types
            return in_array($notification->type, [
                \App\Notifications\GagnantProjetNotifications::class, // Ajouter un autre type de notification ici
            ]);
        });
    }

    public function render()
    {
        return view('livewire.gagnant-negociation');
    }
}

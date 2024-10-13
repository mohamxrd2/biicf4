<?php

namespace App\Livewire;

use Livewire\Component;

class NotifFinanceProjet extends Component
{
    public $notifications = [];
    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications->filter(function ($notification) {
            return $notification->type === \App\Notifications\DemandeCreditProjetNotification::class;
        });
    }
    public function render()
    {
        return view('livewire.notif-finance-projet');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;

class NotifFinance extends Component
{

    public $notifications = [];

    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications;
    }
    public function render()
    {
        return view('livewire.notif-finance');
    }
}

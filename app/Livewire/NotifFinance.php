<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;

class NotifFinance extends Component
{

    public $notifications = [];
    public $userDetails;

    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications->filter(function ($notification) {
            return $notification->type === \App\Notifications\DemandeCreditNotification::class;
        });
    }


    public function render()
    {
        return view('livewire.notif-finance');
    }
}

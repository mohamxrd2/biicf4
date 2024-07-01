<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;


class Notif extends Component
{
    public function render()
    {
        // Récupérer les notifications de l'utilisateur
        $notifications = Auth::user()->notifications;

        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('livewire.notif', compact('notifications', 'unreadCount'));
    }
}

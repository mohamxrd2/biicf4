<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class Notif extends Component
{
    public function render()
    {
        try {
            // Récupérer les notifications de l'utilisateur
            $notifications = Auth::user()->notifications;

            // Compter les notifications non lues
            $unreadCount = Auth::user()->unreadNotifications->count();

            return view('livewire.notif', compact('notifications', 'unreadCount'));
        } catch (\Exception $e) {
            // Log l'erreur
            Log::error('Erreur lors de la récupération des notifications.', ['error' => $e->getMessage()]);

            // Retourner une vue alternative ou afficher un message d'erreur
            session()->flash('error', 'Erreur lors de la récupération des notifications.');

            // Retourner une vue avec des valeurs par défaut pour éviter que l'application ne plante
            return view('livewire.notif', ['notifications' => collect(), 'unreadCount' => 0]);
        }
    }
}

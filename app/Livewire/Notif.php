<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class Notif extends Component
{

    public function deleteNotification($id)
    {
        $notification = Notification::find($id);
        if ($notification) {
            $notification->delete();
            session()->flash('success', 'Notification supprimée avec succès.');
            // Rafraîchissez la liste des notifications
            $notifications = $this->notifications->reject(function($n) use ($id) {
                return $n->id === $id;
            });
        }
    }
    public function render()
    {
        // Récupérer les notifications de l'utilisateur
        $notifications = Auth::user()->notifications;

        $unreadCount = Auth::user()->unreadNotifications->count();

        return view('livewire.notif', compact('notifications', 'unreadCount'));
    }
}

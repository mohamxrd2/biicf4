<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Notification extends Component
{
    public $unreadCount = 0;
    public $notifications;
    protected $layout = 'components.layouts.app';

    public function mount()
    {
        try {
            $user = Auth::user();
            $this->notifications = $user->notifications;
            $this->unreadCount = $user->unreadNotifications->count();
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement des notifications', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function markAsRead($notificationId)
    {
        try {
            $notification = Auth::user()->notifications->find($notificationId);
            if ($notification) {
                $notification->markAsRead();
                $this->unreadCount = Auth::user()->unreadNotifications->count();
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de la notification', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function markAllAsRead()
    {
        try {
            Auth::user()->unreadNotifications->markAsRead();
            $this->unreadCount = 0;
        } catch (\Exception $e) {
            Log::error('Erreur lors du marquage de toutes les notifications', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notification', [
            'notifications' => $this->notifications,
            'unreadCount' => $this->unreadCount
        ]);
    }
}

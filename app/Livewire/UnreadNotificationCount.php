<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UnreadNotificationCount extends Component
{
    public $unreadCount = 0;
    public $userId;

    // Suppression de la propriété $listeners avec le placeholder

    public function mount()
    {
        $this->userId = Auth::guard('web')->id();
        $this->updateUnreadCount();
    }

    // Nouvelle méthode pour définir les écouteurs dynamiquement
    public function getListeners()
    {
        $userId = Auth::guard('web')->id() ?: $this->userId;
        return [
            "echo-private:App.Models.User.{$userId},NotificationSent" => 'refreshCount',
            'notification-received' => 'updateUnreadCount'
        ];
    }

    public function refreshCount()
    {
        $this->dispatch('formSubmitted', 'Vous venez de recevoir une notification');
    }

    public function updateUnreadCount()
    {
        $user = Auth::guard('web')->user();
        $this->unreadCount = $user ? $user->unreadNotifications->count() : 0;
    }

    public function render()
    {
        return view('livewire.unread-notification-count', [
            'unreadCount' => $this->unreadCount
        ]);
    }
}

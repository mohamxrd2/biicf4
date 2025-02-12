<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UnreadNotificationCount extends Component
{
    public $unreadCount = 0;
    public $userId;

    protected $listeners = [
        'echo-private:App.Models.User.{userId},NotificationSent' => 'refreshCount',
        'notification-received' => 'updateUnreadCount'
    ];

    public function mount()
    {
        $this->userId = Auth::guard('web')->id();
        $this->updateUnreadCount();
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

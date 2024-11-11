<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class UnreadNotificationCount extends Component
{
    public $unreadCount = 0;
    public $userId;

    public function mount()
    {
        $this->userId = Auth::guard('web')->id();
        $this->updateUnreadCount();
    }

    #[On('echo:private-App.Models.User.{userId},NotificationSent')]
    public function incrementNotification($event)
    {
        dd($event);

        $this->updateUnreadCount();
    }

    private function updateUnreadCount()
    {
        $user = Auth::guard('web')->user();
        $this->unreadCount = $user ? $user->unreadNotifications->count() : 0;
    }

    public function render()
    {
        return view('livewire.unread-notification-count');
    }
}

<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnreadNotificationCount extends Component
{
    public $unreadCount;

    public function mount()
    {
        $user = Auth::guard('web')->user();
        $this->unreadCount = $user ? $user->unreadNotifications->count() : 0;
    }
    public function render()
    {
        return view('livewire.unread-notification-count');
    }
}

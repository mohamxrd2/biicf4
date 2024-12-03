<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ConfirmationNotif extends Component
{
    public $notification;
    public $id;
    public $user;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $id = Auth::id();
        $this->user = User::findOrFail($id);
    }
    public function render()
    {
        return view('livewire.confirmation-notif');
    }
}

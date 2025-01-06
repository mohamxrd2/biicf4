<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\DatabaseNotification;

class NotificationDetail extends Component
{
    public $notificationId;
    public $notification;
    protected $layout = 'components.layouts.app';

    public function mount($id)
    {
        try {
            $this->notificationId = $id;
            $this->notification = DatabaseNotification::findOrFail($id);

            if ($this->notification->unread()) {
                $this->notification->markAsRead();
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement de la notification', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        return view('livewire.notification-detail');
    }
}

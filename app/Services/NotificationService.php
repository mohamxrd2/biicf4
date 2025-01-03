<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Confirmation;

class NotificationService
{
    public function notifyUsers(array $data, array $userIds)
    {
        foreach ($userIds as $userId) {
            $user = User::find($userId);
            if ($user) {
                Notification::send($user, new Confirmation($data));
            }
        }
    }
}

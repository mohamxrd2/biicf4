<?php

namespace App\Services;

use App\Models\Wallet;
use Exception;

class WalletService
{
    public function updateBalance($userId, $amount)
    {
        $wallet = Wallet::where('user_id', $userId)->firstOrFail();
        $wallet->increment('balance', $amount);
    }
}


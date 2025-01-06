<?php

namespace App\Services;

use App\Models\Transaction;

class TransactionService
{
    public function  createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = 'effectué';
        $transaction->save();
    }
}

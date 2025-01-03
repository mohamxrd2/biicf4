<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\ComissionAdmin;
use Illuminate\Support\Facades\Log;
use Exception;

class CommissionService
{
    /**
     * Handle the distribution of commissions to admins and sponsors.
     *
     * @param float $totalCommissions
     * @return void
     */
    public function handleCommissions(float $totalCommissions): void
    {
        $remainingCommissions = $this->distributeToParrains($totalCommissions);
        $this->distributeToAdmin($remainingCommissions);
    }

    /**
     * Distribute commissions to sponsors (parrains) up to three levels.
     *
     * @param float $commissions
     * @return float Remaining commissions after sponsor distribution
     */
    private function distributeToParrains(float $commissions): float
    {
        $currentParrain = auth()->user()->parrain;
        $level = 1;

        while ($currentParrain && $level <= 3) {
            $parrainUser = User::find($currentParrain);

            if (!$parrainUser) {
                break;
            }

            $parrainWallet = Wallet::where('user_id', $parrainUser->id)->first();

            if ($parrainWallet) {
                $commissionForParrain = $commissions * 0.01; // 1% per level
                $parrainWallet->increment('balance', $commissionForParrain);
                $commissions -= $commissionForParrain;

                Log::info("Commission envoyée au parrain niveau $level", [
                    'parrain_id' => $parrainUser->id,
                    'commission' => $commissionForParrain,
                ]);

                $this->createTransaction(
                    auth()->id(),
                    $parrainUser->id,
                    'Commission',
                    $commissionForParrain,
                    $this->generateTransactionReference(),
                    "Commission niveau $level"
                );
            }

            $currentParrain = $parrainUser->parrain;
            $level++;
        }

        return $commissions;
    }

    /**
     * Distribute remaining commissions to the admin wallet.
     *
     * @param float $commissions
     * @return void
     */
    private function distributeToAdmin(float $commissions): void
    {
        $adminWallet = ComissionAdmin::where('admin_id', 1)->first();

        if ($adminWallet) {
            $adminWallet->increment('balance', $commissions);

            Log::info('Commission envoyée à l\'admin.', [
                'admin_id' => 1,
                'commissions' => $commissions,
            ]);

            $this->createTransaction(
                auth()->id(),
                1,
                'Commission',
                $commissions,
                $this->generateTransactionReference(),
                'Commission de BICF'
            );
        }
    }

    /**
     * Create a transaction record.
     *
     * @param int $userId
     * @param int $receiverId
     * @param string $type
     * @param float $amount
     * @param string $reference
     * @param string $description
     * @return void
     */
    private function createTransaction(int $userId, int $receiverId, string $type, float $amount, string $reference, string $description): void
    {
        // Logic for creating a transaction in the database
        \App\Models\Transaction::create([
            'user_id' => $userId,
            'receiver_id' => $receiverId,
            'type' => $type,
            'amount' => $amount,
            'reference' => $reference,
            'description' => $description,
        ]);
    }

    /**
     * Generate a unique transaction reference.
     *
     * @return string
     */
    private function generateTransactionReference(): string
    {
        return strtoupper(uniqid('TRX'));
    }
}

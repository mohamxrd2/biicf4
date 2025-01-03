<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\User;
use App\Models\ComissionAdmin;
use App\Models\Transaction;
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
                Log::warning('Parrain introuvable', [
                    'parrain_id' => $currentParrain,
                    'level' => $level,
                ]);
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
                    $this->generateIntegerReference(),
                    "Commission niveau $level",
                    'COC'
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
                1, // ID de l'admin
                'Commission',
                $commissions,
                $this->generateIntegerReference(),
                'Commission de BICF',
                'COC'
            );
        } else {
            Log::error('Erreur : Portefeuille admin introuvable', [
                'admin_id' => 1,
                'commissions' => $commissions,
            ]);
        }
    }

    /**
     * Create a transaction record.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string $type
     * @param float $amount
     * @param int $reference_id
     * @param string $description
     * @param string $type_compte
     * @return void
     */
    public function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $type_compte): void
    {
        // Vérifiez si l'utilisateur receiver existe
        $receiverUser = User::find($receiverId);

        if (!$receiverUser) {
            Log::error('Erreur de transaction : Utilisateur receiver introuvable', [
                'receiver_id' => $receiverId,
                'sender_id' => $senderId,
                'type' => $type,
                'amount' => $amount,
            ]);
            return; // Arrêtez l'exécution si l'utilisateur n'existe pas
        }

        // Créez la transaction
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

        Log::info('Transaction créée avec succès', [
            'transaction_id' => $transaction->id,
            'receiver_id' => $receiverId,
            'sender_id' => $senderId,
        ]);
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}

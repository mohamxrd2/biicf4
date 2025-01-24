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
    public function handleCommissions(float $totalCommissions, int $parainFournisseur): void
    {
        $remainingCommissions = $this->distributeToParrains($totalCommissions, $parainFournisseur);
        $this->distributeToAdmin($remainingCommissions);
    }

    /**
     * Distribute commissions to sponsors (parrains) up to three levels.
     *
     * @param float $commissions
     * @return float Remaining commissions after sponsor distribution
     */
    private function distributeToParrains(float $commissions, $parainFournisseur)
    {
        try {
            $transactionService = new TransactionService();

            $currentParrain = auth()->user()->parrain;
            $level = 1;

            $parrainUser = User::find($currentParrain);
            $parainFournisseur = User::find($parainFournisseur);

            if (!$parrainUser && !$parainFournisseur) {
                Log::warning('Parrain introuvable', [
                    'parrain_id' => $currentParrain,
                    '$parainFournisseur' => $parainFournisseur,
                ]);
                return $commissions;
            }

            // Process parrains automatically
            foreach ([$parrainUser, $parainFournisseur] as $parrain) {
                if (!$parrain) continue;

                $wallet = Wallet::firstOrCreate(
                    ['user_id' => $parrain->id],
                    ['balance' => 0]
                );

                $commission = $commissions * 0.10;
                $wallet->increment('balance', $commission);
                $commissions -= $commission;


                $transactionService->createTransaction(
                    auth()->id(),
                    $parrain->id,
                    'Commission',
                    $commission,
                    $this->generateIntegerReference(),
                    "Commission de parrainage niveau $level",
                    'COC'
                );

                Log::info("Commission envoyée au parrain niveau $level", [
                    'parrain_id' => $parrain->id,
                    'commission' => $commission,
                ]);
            }

            return $commissions;
        } catch (Exception $e) {
            Log::error('Erreur lors de la distribution aux parrains:', [
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Distribute remaining commissions to the admin wallet.
     *
     * @param float $commissions
     * @return void
     */
    private function distributeToAdmin(float $commissions): void
    {
        try {
            $transactionService = new TransactionService();

            $adminWallet = ComissionAdmin::firstOrCreate(
                ['admin_id' => 1],
                ['balance' => 0]
            );

            $adminWallet->increment('balance', $commissions);

            Log::info('Commission envoyée à l\'admin.', [
                'admin_id' => 1,
                'commissions' => $commissions,
            ]);
            
            $transactionService->createTransaction(
                auth()->id(),
                1, // ID de l'admin
                'Commission',
                $commissions,
                $this->generateIntegerReference(),
                'Commission de BICF',
                'COC'
            );

        } catch (Exception $e) {
            Log::error('Erreur lors de la distribution à l\'admin:', [
                'error' => $e->getMessage(),
                'admin_id' => 1,
                'commissions' => $commissions,
            ]);
            throw $e;
        }
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}

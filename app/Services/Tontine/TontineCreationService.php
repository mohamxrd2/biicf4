<?php

namespace App\Services\Tontine;

use App\Models\Tontines;
use App\Models\gelement;
use App\Models\Wallet;
use App\Services\TransactionService;
use Illuminate\Support\Str;

class TontineCreationService
{
    private $calculationService;
    private $transactionService;

    public function __construct(
        TontineCalculationService $calculationService,
        TransactionService $transactionService
    ) {
        $this->calculationService = $calculationService;
        $this->transactionService = $transactionService;
    }

    public function createTontine(array $data, int $userId): bool
    {
        try {
            $startDate = $data['server_time'];
            $endDate = $this->calculationService->calculateEndDate(
                $startDate,
                $data['frequency'],
                $data['duration']
            );

            $nextPaymentDate = $this->calculationService->calculateNextPaymentDate(
                $startDate,
                $data['frequency']
            );

            $calculations = $this->calculationService->calculatePotentialGain(
                $data['amount'],
                $data['duration']
            );

            $reference_id = $this->generateIntegerReference();

            // Mise à jour du wallet
            $userWallet = Wallet::where('user_id', $userId)->first();
            $userWallet->decrement('balance', $data['amount']);

            // Création de la transaction
            $this->transactionService->createTransaction(
                $userId,
                $userId,
                'Réception',
                $data['amount'],
                $reference_id,
                'Paiement pour achat.',
                'COC'
            );

            // Création de l'élément gelé
            gelement::create([
                'reference_id' => $reference_id,
                'id_wallet' => $userWallet->id,
                'amount' => $data['amount'],
            ]);

            // Création de la tontine
            Tontines::create([
                'date_debut' => $startDate->format('Y-m-d'),
                'montant_cotisation' => $data['amount'],
                'montant_total' => $calculations['montant_total'],
                'frequence' => $data['frequency'],
                'date_fin' => $endDate,
                'next_payment_date' => $nextPaymentDate,
                'frais_gestion' => $calculations['frais_gestion'],
                'user_id' => $userId,
            ]);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function generateUniqueReference(): string
    {
        return 'REF-' . strtoupper(Str::random(6));
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}

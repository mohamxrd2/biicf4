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
                $data['duration'],
            );
            $gain_potentiel = ($data['amount'] * $data['duration']);

            $reference_id = $this->generateIntegerReference();

            // Mise à jour du wallet
            $userWallet = Wallet::where('user_id', $userId)->first();

            if (!$userWallet || $userWallet->balance < $data['amount']) {
                throw new \Exception('Solde insuffisant pour créer la tontine.');
            }

            $userWallet->decrement('balance', $data['amount']);



            // Création de l'élément gelé
            gelement::create([
                'reference_id' => $this->generateUniqueReference(),
                'id_wallet' => $userWallet->id,
                'amount' => $data['amount'],
            ]);

            // Création de la tontine
            $Tontines = Tontines::create([
                'date_debut' => $startDate->format('Y-m-d'),
                'montant_cotisation' => $data['amount'],
                'frequence' => $data['frequency'],
                'date_fin' => $endDate,
                'next_payment_date' => $nextPaymentDate,
                'gain_potentiel' => $calculations['gain_potentiel'],
                'nombre_cotisations' => $data['duration'],
                'frais_gestion' => $calculations['frais_gestion'],
                'user_id' => $userId,
                'nb_cotisations' => $data['duration'],
                'gain_potentiel' => $gain_potentiel
            ]);

            // Création de la transaction
            $this->transactionService->createTransaction(
                $userId,
                $userId,
                'Gele',
                $data['amount'],
                $reference_id,
                "Gelemment pour tontine . {$Tontines->id}",
                'COC'
            );

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

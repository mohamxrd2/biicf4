<?php

namespace App\Services\Tontine;

use App\Models\Tontines;
use App\Models\Gelement;
use App\Models\TontineUser;
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

    public function createTontine(array $data, int $userId, bool $isUnlimited = false): bool
    {
        try {
            // Si la tontine est illimitée, on attribue la durée minimale
            if ($isUnlimited) {
                $data['duration'] = $this->getMinDuration($data['frequency']);
            }

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

            $reference_id = $this->generateIntegerReference();

            // Mise à jour du wallet
            $userWallet = Wallet::where('user_id', $userId)->first();

            if (!$userWallet || $userWallet->balance < $data['amount']) {
                throw new \Exception('Solde insuffisant pour créer la tontine.');
            }

            $userWallet->decrement('balance', $data['amount']);

            // Créer un nouveau gelement spécifique pour cette tontine
            $gelementReference = $this->generateUniqueReference();
            $gelement = Gelement::create([
                'reference_id' => $gelementReference,
                'id_wallet' => $userWallet->id,
                'amount' => $data['amount'],
                'status' => 'pending' // Ajout d'un statut initial
            ]);

            // Définir la durée et la date de fin en fonction de unlimited
            $nombreCotisations = $isUnlimited ? null : $data['duration'];
            $dateFin = $isUnlimited ? null : $endDate;
            $gain_potentiel = $isUnlimited ? null : $calculations['montant_total'];


            // Création de la tontine
            $Tontines = Tontines::create([
                'date_debut' => $startDate->format('Y-m-d'),
                'montant_cotisation' => $data['amount'],
                'frequence' => $data['frequency'],
                'date_fin' => $dateFin,
                'next_payment_date' => $nextPaymentDate,
                'gain_potentiel' => $gain_potentiel,
                'nombre_cotisations' => $nombreCotisations,
                'frais_gestion' => $calculations['frais_gestion'],
                'user_id' => $userId,
                'statut' => '1st',
                'gelement_reference' => $gelementReference, // Ajout de la référence du gelement
                'isUnlimited' => $isUnlimited // Ajout de la référence du gelement

            ]);

            $TontineUser = TontineUser::create([
                'tontine_id' => $Tontines->id,
                'user_id' => $userId,
            ]);

            // Création de la transaction
            $this->transactionService->createTransaction(
                $userId,
                $userId,
                'Gele',
                $data['amount'],
                $reference_id,
                "Gelemment pour tontine #00{$Tontines->id}",
                'COC'
            );

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
    private function getMinDuration(string $frequency): int
    {
        return match ($frequency) {
            'quotidienne' => 30,
            'hebdomadaire' => 4,
            'mensuelle' => 3,
            default => 1,
        };
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

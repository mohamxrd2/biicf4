<?php

namespace App\Console\Commands;

use App\Models\Cefp;
use App\Models\Tontines;
use Illuminate\Console\Command;
use App\Jobs\ProcessPayment;
use App\Models\User;
use App\Models\Wallet;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TontineEpargne extends Command
{
    protected $signature = 'tontine:process-payments';
    protected $description = 'Prélève les cotisations des tontines en fonction de la période choisie.';

    private RecuperationTimer $recuperationTimer;

    public function __construct(RecuperationTimer $recuperationTimer)
    {
        parent::__construct();
        $this->recuperationTimer = $recuperationTimer;
    }

    public function handle()
    {
        try {
            // Synchronisation de l'heure du serveur
            $timeSync = new TimeSyncService($this->recuperationTimer);
            $result = $timeSync->getSynchronizedTime();

            if (!$result || empty($result['timestamp'])) {
                Log::error("Échec de la synchronisation de l'heure du serveur. Arrêt du script.");
                return 1;
            }

            $currentDate = Carbon::createFromTimestamp($result['timestamp'])->startOfDay();

            Log::info("Traitement des paiements pour le jour : " . $currentDate->toDateString());

            $allTontines = Tontines::where('statut', '!=', 'inactive')->get();

            if ($allTontines->isEmpty()) {
                Log::info("Aucune tontine active à traiter.");
                return;
            }

            DB::transaction(function () use ($allTontines, $currentDate) {
                foreach ($allTontines as $tontine) {
                    $this->processTontinePaiements($tontine);
                }
            });

            return 0;
        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement des tontines: " . $e->getMessage());
            return 1;
        }
    }
    private function getMinDuration(string $frequency): int
    {
        return match ($frequency) {
            'quotidienne' => 7,
            'hebdomadaire' => 4,
            'mensuelle' => 3,
            default => 1,
        };
    }
    private function deductServiceFees(Tontines $tontine)
    {
        // Pour les paiements réguliers, vérifier le solde disponible
        // en tenant compte des autres tontines actives
        $frais = $tontine->montant_cotisation;
        // Retirer les frais du wallet de l'utilisateur
        $wallet = Wallet::where('user_id', $tontine->user_id)->first();
        if ($wallet && $wallet->balance >= $frais) {
            $userCedd = Cefp::where('id_wallet', $wallet->id)->first();
            $userCedd->decrement('Solde', $frais);
            $userCedd->save();
        } else {
            Log::warning("Solde insuffisant pour les frais de service de la tontine ID: {$tontine->id}");
        }
    }
    private function processTontinePaiements(Tontines $tontine)
    {
        try {
            // Load users with eager loading to avoid N+1 query problem
            $users = $tontine->users()->with('wallet')->get();

            foreach ($users as $user) {
                dispatch(new ProcessPayment($user, $tontine))
                    ->onQueue('default')
                    ->afterCommit();
            }

            $currentPaymentDate = Carbon::parse($tontine->next_payment_date);

            // Calculer la prochaine date de paiement
            $nextPaymentDate = match ($tontine->frequence) {
                'quotidienne' => $currentPaymentDate->addDay(),
                'hebdomadaire' => $currentPaymentDate->addWeek(),
                'mensuelle' => $currentPaymentDate->addMonth(),
                default => throw new \InvalidArgumentException("Fréquence invalide: {$tontine->frequence}")
            };

            if ($tontine->isUnlimited) {
                // Incrémenter la durée de 1
                $tontine->increment('nombre_cotisations', 1);

                // Vérifier si la durée atteint le minimum requis
                $minDuration = $this->getMinDuration($tontine->frequence);
                if ($tontine->nombre_cotisations >= $minDuration) {
                    // Prélever les frais de service
                    $this->deductServiceFees($tontine);

                    // Réinitialiser la durée à zéro
                    $tontine->update([
                        'nombre_cotisations' => 0,
                        'statut' => 'inactive'
                    ]);
                }

                // Mettre à jour la prochaine date de paiement
                $tontine->update([
                    'next_payment_date' => $nextPaymentDate->toDateString()
                ]);
            } else {
                // Si limité, vérifier si on dépasse la date de fin
                $dateFin = Carbon::parse($tontine->date_fin);

                if ($nextPaymentDate->lte($dateFin)) {
                    $tontine->update([
                        'next_payment_date' => $nextPaymentDate->toDateString()
                    ]);
                } else {
                    $tontine->update([
                        'statut' => 'inactive',
                        'next_payment_date' => null
                    ]);
                }
            }

            Log::info("Traitement réussi pour la tontine ID: {$tontine->id}");
        } catch (\Exception $e) {
            Log::error("Erreur lors du traitement de la tontine {$tontine->id}: " . $e->getMessage());
            throw $e;
        }
    }
}

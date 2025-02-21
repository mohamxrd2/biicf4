<?php

namespace App\Console\Commands;

use App\Models\Tontines;
use Illuminate\Console\Command;
use App\Jobs\ProcessPayment;
use App\Models\User;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TontineEpargne extends Command
{
    protected $signature = 'tontine:process-payments';
    protected $description = 'Prélève les cotisations des tontines en fonction de la période choisie.';
    private $recuperationTimer;
    public $time;
    public $error;
    public $timestamp;

    public function __construct(RecuperationTimer $recuperationTimer)
    {
        parent::__construct();
        $this->recuperationTimer = $recuperationTimer;
    }

    public function handle()
    {
        // Synchronisation de l'heure du serveur
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();

        if (!$result || empty($result['timestamp'])) {
            Log::error("Échec de la synchronisation de l'heure du serveur. Arrêt du script.");
            return;
        }

        $serverTime = $result['timestamp'];
        Log::info("Heure du serveur synchronisée : {$serverTime}");

        try {
            $tontines = Tontines::where('next_payment_date', '<=', now())
                ->where('date_fin', '>=', now())
                ->get();

            Log::info("liste des tontines : {$tontines}");

            foreach ($tontines as $tontine) {
                DB::beginTransaction();
                try {
                    // Vérifier si le paiement est dû
                    if ($tontine->next_payment_date > now()) {
                        Log::info("Le paiement pour la tontine {$tontine->id} n'est pas encore dû.");
                        DB::commit();
                        continue;
                    }

                    // Vérifier si la tontine a bien des utilisateurs avant d'exécuter les paiements
                    $users = $tontine->users;


                    if ($users->isEmpty()) {
                        Log::warning("Aucun utilisateur trouvé pour la tontine {$tontine->nom}. Aucun paiement exécuté.");
                    } else {
                        foreach ($users as $user) {
                            if (!$user instanceof User) {
                                Log::error("Données utilisateur invalides pour la tontine {$tontine->nom}.");
                                continue;
                            }

                            try {
                                dispatch(new ProcessPayment($user, $tontine))
                                    ->onQueue('default')
                                    ->afterCommit();

                                Log::info("Paiement de {$tontine->montant_cotisation} exécuté pour {$user->name} dans la tontine {$tontine->nom}.");
                            } catch (\Exception $e) {
                                Log::error("Erreur lors du traitement du paiement pour {$user->name} dans la tontine {$tontine->nom} : " . $e->getMessage());
                            }
                        }
                    }

                    // Calcul de la nouvelle date de paiement
                    $nextDayPayment = match ($tontine->frequence) {
                        'quotidienne' => Carbon::parse($tontine->next_payment_date)->addDay(),
                        'hebdomadaire' => Carbon::parse($tontine->next_payment_date)->addWeek(),
                        'mensuelle' => Carbon::parse($tontine->next_payment_date)->addMonth(),
                        default => throw new \Exception("Fréquence inconnue pour la tontine {$tontine->nom}")
                    };

                    Log::info("Nouvelle date de paiement pour {$tontine->nom} : {$nextDayPayment}");

                    // Vérifier si la nouvelle date dépasse la date de fin
                    if ($nextDayPayment > $tontine->date_fin) {
                        Log::info("Tontine {$tontine->nom} terminée. Plus de prélèvements.");
                        DB::commit();
                        continue;
                    }

                    // Mise à jour dans la base de données
                    $tontine->update(['next_payment_date' => $nextDayPayment]);

                    DB::commit();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error("Erreur lors du traitement de la tontine {$tontine->nom} : " . $e->getMessage());
                }
            }
        } catch (\Exception $e) {
            Log::error('Erreur globale lors du traitement des paiements : ' . $e->getMessage());
            $this->error('Erreur lors du traitement des paiements. Vérifiez les logs.');
        }
    }
}

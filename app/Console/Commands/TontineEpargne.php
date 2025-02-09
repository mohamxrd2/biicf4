<?php

namespace App\Console\Commands;

use App\Models\Tontines;
use Illuminate\Console\Command;
use App\Jobs\ProcessPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TontineEpargne extends Command
{
    protected $signature = 'tontine:process-payments';
    protected $description = 'Prélève les cotisations des tontines en fonction de la période choisie.';

    public function handle()
    {
        DB::beginTransaction();

        try {
            $tontines = Tontines::with('users')
                ->where('next_payment_date', '<=', now()) // Vérifie si le paiement est dû
                ->where('end_date', '>=', now()) // Assure que la tontine n'est pas expirée
                ->get();

            foreach ($tontines as $tontine) {
                foreach ($tontine->users as $user) {
                    // Vérifier si l'utilisateur a assez de solde
                    dispatch(new ProcessPayment($user, $tontine));

                    Log::info("Paiement de {$tontine->montant_cotisation} effectué pour {$user->name} dans la tontine {$tontine->nom}.");
                }

                // Mise à jour de la prochaine date de paiement
                $tontine->next_payment_date = match ($tontine->frequency) {
                    'quotidienne' => Carbon::parse($tontine->next_payment_date)->addDay(),
                    'hebdomadaire' => Carbon::parse($tontine->next_payment_date)->addWeek(),
                    'mensuelle' => Carbon::parse($tontine->next_payment_date)->addMonth(),
                };

                // Vérifier si la nouvelle date dépasse la date de fin
                if ($tontine->next_payment_date > $tontine->end_date) {
                    Log::info("Tontine {$tontine->nom} terminée. Plus de prélèvements.");
                    continue;
                }

                $tontine->save();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement des paiements : ' . $e->getMessage());
            $this->error('Erreur lors du traitement des paiements. Vérifiez les logs.');
        }
    }
}

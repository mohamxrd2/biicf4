<?php

namespace App\Console\Commands;

use App\Jobs\EchecDeRemboursement;
use App\Jobs\GérerPortionRemboursement;
use App\Jobs\TraiterRemboursementCredit;
use App\Services\CommissionService;
use App\Services\TransactionService;
use Exception;
use Carbon\Carbon;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\Crp;
use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use GuzzleHttp\Client;
use App\Models\credits;
use App\Models\Transaction;
use App\Events\PortionUpdated;
use App\Models\ComissionAdmin;
use App\Models\credits_groupé;
use App\Models\projets_accordé;
use Illuminate\Console\Command;
use App\Events\NotificationSent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\remboursement;
use Illuminate\Support\Facades\Auth;
use App\Models\portions_journalieres;
use App\Notifications\PortionJournaliere;
use App\Models\transactions_remboursement;
use Illuminate\Support\Facades\Notification;

class RappelPortionsJournalCredits extends Command
{

    protected $signature = 'app:rappel-journalieres-credits';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Date du jour
        $dateDuJour = Carbon::today()->toDateString();

        // Récupérer tous les crédits
        $credits = credits_groupé::where('statut', 'en cours')->get();

        foreach ($credits as $credit) {

            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour <= $credit->date_fin) {

                $portionCapital = $credit->montant;
                $portionInteret = $credit->taux_interet;
                $SommeApaye = $credit->portion_journaliere;
                // Récupérer le wallet de l'utilisateur
                $wallet = Wallet::where('user_id', $credit->emprunteur_id)->first();
                $crp = $wallet->crp;
                if (!$wallet && !$crp) {
                    Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $credit->emprunteur_id);
                    continue;
                }

                $emprunteur = User::find($credit->emprunteur_id);
                if (!$emprunteur) {
                    throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                }

                DB::beginTransaction();
                try {
                    $montantASoustraire = min($SommeApaye, $credit->montan_restantt);
                    // Soustraire le montant ajusté du montant restant dans le crédit
                    $credit->montan_restantt -= $montantASoustraire;

                    // Vérifier si le crédit est totalement remplie & remboursement
                    if ($credit->montan_restantt == 0) {
                        $balanceSuffisante = $wallet->balance >= $SommeApaye;
                        if ($balanceSuffisante) {
                            $credit->statut = "remboursé";

                            dispatch(new TraiterRemboursementCredit($credit, $wallet));
                        } elseif ($wallet->balance < $SommeApaye) {
                            dispatch(new EchecDeRemboursement(
                                $credit,
                                $portionCapital,
                                $portionInteret,
                                'Le solde de votre compte est insuffisant., Veuillez recharger votre compte .'
                            ));
                        }
                    }

                    $credit->save();

                    dispatch(new GérerPortionRemboursement(
                        $credit,
                        $this->generateIntegerReference(),
                        $SommeApaye,
                        $portionInteret,
                        $dateDuJour
                    ));

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Erreur lors de l\'ajout du montant : ' . $e->getMessage());
                }
            }
        }
    }
    protected function generateIntegerReference(): int
    {
        return (int) (now()->getTimestamp() * 1000 + now()->micro);
    }
}

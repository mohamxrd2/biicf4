<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\Cfa;
use App\Models\credits;
use App\Models\Crp;
use App\Models\portions_journalieres;
use App\Models\Transaction;
use App\Models\transactions_remboursement;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PortionJournaliere;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class RappelPortionsJournalCredits extends Command
{

    protected $signature = 'app:rappel-journalieres-credits';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Date du jour
        $dateDuJour = Carbon::today()->toDateString();
        Log::info('Date du jour : ' . $dateDuJour);

        // Récupérer tous les crédits
        $credits = credits::where('statut', 'en_cours')->get();
        Log::info('Nombre de credits avec statut "en cours" : ' . $credits->count());

        foreach ($credits as $credit) {
            Log::info('Le crédit avec ID ' . $credit->id);
            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour <= $credit->date_fin) {
                Log::info('Le crédit avec ID ' . $credit->id . ' est actif aujourd\'hui.');

                // Vérifier si la portion a déjà été enregistrée pour cette date
                $existingPortion = portions_journalieres::where('id_user', $credit->emprunteur_id)
                    ->where('date_portion', $dateDuJour)
                    ->first();

                if (!$existingPortion) {
                    Log::warning("Aucune portion trouvée.");
                    continue;
                }

                $portionCapital = $credit->montant;
                $portionInteret = $credit->taux_interet;

                $montantTotal = $credit->portion_journaliere;

                Log::info("Montants récupérés pour le crédit ID: " . $credit->id, [
                    'portion_capital' => $portionCapital,
                    'portion_interet' => $portionInteret,
                    'montant_total' => $montantTotal
                ]);

                // Récupérer le wallet de l'utilisateur
                $wallet = Wallet::where('user_id', $credit->emprunteur_id)->first();
                $crp = Crp::where('id_wallet', $wallet->id)->first();

                if (!$wallet && !$crp) {
                    Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $credit->emprunteur_id);
                    continue;
                }

                
                DB::beginTransaction();
                try {
                    $balanceSuffisante = $wallet->balance >= $montantTotal;

                    if ($balanceSuffisante ) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($credit->emprunteur_id);
                        Log::info('emprunteur ID : ' . $credit->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                        }

                        // Calculer le montant réel à soustraire (ne pas dépasser le montant restant du crédit)
                        $montantASoustraire = min($montantTotal, $credit->montant_restant);

                        // Mettre à jour le solde du wallet et du CRP avec le montant ajusté
                        $wallet->balance -= $montantASoustraire;
                        $wallet->save();

                        if ($crp) {
                            $crp->Solde += $montantASoustraire;
                            $crp->save();
                        }

                        // Soustraire le montant ajusté du montant restant dans le crédit
                        $credit->montant_restant -= $montantASoustraire;

                        // Vérifier si le crédit est totalement remboursé
                        if ($credit->montant_restant == 0) {
                            $credit->statut = "remboursé";
                        }

                        $credit->save();

                        $reference_id = $this->generateIntegerReference();



                        $this->remboursement(
                            $credit->id,
                            $reference_id,
                            $credit->emprunteur_id,
                            $credit->emprunteur_id,
                            $montantTotal,
                            $portionInteret,
                            $dateDuJour,
                            'effectué'
                        );


                        Notification::send($emprunteur, new PortionJournaliere($credit, $portionCapital, $portionInteret));
                        Log::info('Notification envoyée pour le crédit ID : ' . $credit->id);

                        // Après l'envoi de la notification
                        event(new NotificationSent($emprunteur));
                        event(new PortionUpdated($credit->id, $credit->emprunteur_id, $credit->montan_restantt));
                    } elseif ($wallet->balance < $montantTotal) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($credit->emprunteur_id);
                        Log::info('emprunteur ID : ' . $credit->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                        }
                        $message = 'Le solde de votre compte est insuffisant. Veuillez recharger votre compte pour effectuer cette opération.';
                        Notification::send($emprunteur, new PortionJournaliere($credit, $portionCapital, $portionInteret, $message));

                        // Après l'envoi de la notification
                        event(new NotificationSent($emprunteur));

                        Log::info('Notification envoyée pour solde insuffisant.');
                    }

                    DB::commit();
                } catch (Exception $e) {
                    DB::rollBack();
                    Log::error('Erreur lors de l\'ajout du montant : ' . $e->getMessage());
                }
            }
        }
    }

    protected function remboursement(int $creditId, int $reference_id, int $emprunteurId, int $investisseurId, float $montant, float $interet, string $date, string $status): void
    {
        $transaction = new transactions_remboursement();
        $transaction->credit_id = $creditId;
        $transaction->reference_id = $reference_id;
        $transaction->emprunteur_id = $emprunteurId;
        $transaction->investisseur_id = $investisseurId;
        $transaction->montant = $montant;
        $transaction->interet = $interet;
        $transaction->date_transaction = $date;
        $transaction->statut = $status;
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        return (int) (now()->getTimestamp() * 1000 + now()->micro);
    }
}

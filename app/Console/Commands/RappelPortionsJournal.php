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

class RappelPortionsJournal extends Command
{

    protected $signature = 'app:rappel-journalieres';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Date du jour
        $dateDuJour = Carbon::today()->toDateString();

        // Récupérer tous les crédits
        $credits = credits::where('statut', 'en_cours')->get();

        foreach ($credits as $credit) {
            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour >= $credit->date_debut && $dateDuJour <= $credit->date_fin) {
                // Vérifier si la portion a déjà été enregistrée pour cette date
                $existingPortion = portions_journalieres::where('credit_id', $credit->id)
                    ->where('date_portion', $dateDuJour)
                    ->first();

                if (!$existingPortion) {
                    Log::warning("Aucune portion trouvée pour la date du jour.");
                    continue;
                }

                $portionCapital = $existingPortion->portion_capital;
                $portionInteret = $existingPortion->portion_interet;
                $montantTotal = $portionCapital + $portionInteret;
                Log::info("Montants récupérés pour le crédit ID: " . $credit->id, [
                    'portion_capital' => $portionCapital,
                    'portion_interet' => $portionInteret,
                    'montant_total' => $montantTotal
                ]);

                // Récupérer le wallet de l'utilisateur
                $wallet = Wallet::where('user_id', $credit->emprunteur_id)->first();
                if (!$wallet) {
                    Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $credit->emprunteur_id);
                    continue;
                }

                // Décoder les investisseurs depuis le JSON dans le crédit de la portion
                $decodedProdUsers = [];
                $investisseursIds = $existingPortion->credit->investisseurs;

                foreach ($investisseursIds as $investisseursId) {
                    $decodedValues = json_decode($investisseursId, true);
                    if (is_array($decodedValues)) {
                        $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
                    }
                }

                // Log des IDs des investisseurs
                Log::info('Liste des IDs des investisseurs : ' . implode(', ', $investisseursIds));

                DB::beginTransaction();
                try {
                    if ($wallet->balance >= $montantTotal) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($credit->emprunteur_id);
                        Log::info('emprunteur ID : ' . $credit->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                        }

                        // Mettre à jour le solde du wallet et du CRP
                        $wallet->balance -= $montantTotal;
                        $wallet->save();

                        $crp = Crp::where('id_wallet', $wallet->id)->first();
                        if ($crp) {
                            $crp->Solde += $montantTotal;
                            $crp->save();
                        }


                        // Soustraire le montant total du montant restant dans le crédit
                        $credit->montant_restant -= $montantTotal;
                        $credit->save();

                        $reference_id = $this->generateIntegerReference();

                        foreach ($investisseursIds as $investisseurId) {
                            Log::info("Début de la transaction pour l'investisseur ID: " . $investisseurId);

                            $this->createTransaction(
                                $credit->emprunteur_id,
                                $investisseurId,
                                'Reception',
                                $portionCapital,
                                $reference_id,
                                'Fond conservé pour remboursement de crédit',
                                'Gele'
                            );

                            $this->remboursement(
                                $credit->id,
                                $reference_id,
                                $credit->emprunteur_id,
                                $investisseurId,
                                $montantTotal,
                                $portionInteret,
                                $dateDuJour,
                                'effectué'
                            );
                            broadcast(new PortionUpdated($credit->id, $credit->emprunteur_id, $credit->montant_restant));
                        }

                        Notification::send($emprunteur, new PortionJournaliere($credit, $portionCapital, $portionInteret));
                        Log::info('Notification envoyée pour le crédit ID : ' . $credit->id);
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

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->save();
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

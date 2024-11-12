<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\credits;
use App\Models\Crp;
use App\Models\portions_journalieres;
use App\Models\projets_accordé;
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

class RappelPortionsJournalProjets extends Command
{

    protected $signature = 'app:rappel-journalieres-projets';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Date du jour
        $dateDuJour = Carbon::today()->toDateString();
        Log::info('Date du jour : ' . $dateDuJour);

        // Récupérer tous les crédits
        $projets = projets_accordé::where('statut', 'en cours')->get();
        Log::info('Nombre de projets avec statut "en cours" : ' . $projets->count());

        foreach ($projets as $projet) {
            Log::info('Projet ID : ' . $projet->id . ' - Emprunteur ID : ' . $projet->emprunteur_id);

            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour >= $projet->date_debut && $dateDuJour <= $projet->date_fin) {
                // Vérifier si la portion a déjà été enregistrée pour cette date
                $existingPortion = portions_journalieres::where('id_projet_accord', $projet->id)
                    ->where('date_portion', $dateDuJour)
                    ->first();

                if (!$existingPortion) {
                    Log::warning("Aucune portion trouvée pour la date du jour.");
                    continue;
                }

                $portionCapital = $existingPortion->portion_capital;
                $portionInteret = $existingPortion->portion_interet;
                $montantTotal = $portionCapital + $portionInteret;
                Log::info("Montants récupérés pour le crédit ID: " . $projet->id, [
                    'portion_capital' => $portionCapital,
                    'portion_interet' => $portionInteret,
                    'montant_total' => $montantTotal
                ]);

                // Récupérer le wallet de l'utilisateur
                $wallet = Wallet::where('user_id', $projet->emprunteur_id)->first();
                if (!$wallet) {
                    Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $projet->emprunteur_id);
                    continue;
                }





                DB::beginTransaction();
                try {
                    if ($wallet->balance >= $montantTotal) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($projet->emprunteur_id);
                        Log::info('emprunteur ID : ' . $projet->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $projet->id);
                        }

                        // Calculer le montant réel à soustraire (ne pas dépasser le montant restant du crédit)
                        $montantASoustraire = min($montantTotal, $projet->montan_restantt);

                        // Mettre à jour le solde du wallet et du CRP avec le montant ajusté
                        $wallet->balance -= $montantASoustraire;
                        $wallet->save();

                        $crp = Crp::where('id_wallet', $wallet->id)->first();
                        if ($crp) {
                            $crp->Solde += $montantASoustraire;
                            $crp->save();
                        }

                        // Soustraire le montant ajusté du montant restant dans le crédit
                        $projet->montan_restantt -= $montantASoustraire;

                        // Vérifier si le crédit est totalement remboursé
                        if ($projet->montan_restantt == 0) {
                            $projet->statut = "remboursé";

                            // Décoder les investisseurs depuis le JSON
                            $investisseursJson = $existingPortion->projet->investisseurs; // Chaîne JSON

                            // Décodage du JSON en tableau PHP
                            $decodedInvestisseurs = json_decode($investisseursJson, true);

                            // Vérifier que le JSON est correctement formaté
                            if (is_array($decodedInvestisseurs)) {
                                // Total de l'investissement global pour calculer les parts de chaque investisseur
                                $totalInvestissement = array_sum(array_column($decodedInvestisseurs, 'montant_finance'));

                                // Récupérer le portefeuille principal (CRP) pour déduire le montant total
                                $crpWallet = Crp::where('id_wallet', $wallet->id)->first();

                                if ($crpWallet && $crpWallet->Solde >= $totalInvestissement) { // Assurez-vous que le solde est suffisant
                                    foreach ($decodedInvestisseurs as $investisseur) {
                                        // Vérifier que l'ID de l'investisseur et le montant investi sont définis
                                        if (isset($investisseur['investisseur_id'], $investisseur['montant_finance'])) {
                                            $investisseurId = $investisseur['investisseur_id'];
                                            $montantInvesti = $investisseur['montant_finance'];

                                            // Calculer la part de remboursement pour cet investisseur
                                            $montantRembourse = ($montantInvesti / $totalInvestissement) * $montantASoustraire;

                                            // Récupérer le portefeuille de l'investisseur
                                            $investisseurWallet = Wallet::where('id_user', $investisseurId)->first();

                                            if ($investisseurWallet) {
                                                // Ajouter le montant remboursé au portefeuille de l'investisseur
                                                $investisseurWallet->Solde += $montantRembourse;
                                                $investisseurWallet->save();

                                                // Enregistrer la transaction (optionnel)
                                                $reference_id = $this->generateIntegerReference();

                                                $this->createTransaction(
                                                    $projet->emprunteur_id,
                                                    $investisseurId,
                                                    'Reception',
                                                    $portionCapital,
                                                    $reference_id,
                                                    'Fond conservé pour remboursement de crédit',
                                                    'Gele'
                                                );
                                            }
                                        }
                                    }

                                    // Déduire le montant total remboursé du portefeuille CRP
                                    $crpWallet->Solde -= $montantASoustraire;
                                    $crpWallet->save();

                                    Log::info("Montant total de {$montantASoustraire} remboursé et distribué aux investisseurs.");
                                } else {
                                    Log::error("Solde insuffisant dans le portefeuille CRP pour effectuer le remboursement.");
                                }
                            } else {
                                Log::error("Format incorrect des investisseurs dans le JSON.");
                            }
                        }


                        $projet->save();

                        $reference_id = $this->generateIntegerReference();

                        Log::info("Début de la transaction pour l'utilisateur ID: " . $projet->emprunteur_id);



                        $this->remboursement(
                            $projet->id,
                            $reference_id,
                            $projet->emprunteur_id,
                            $projet->emprunteur_id,
                            $montantTotal,
                            $portionInteret,
                            $dateDuJour,
                            'effectué'
                        );



                        Notification::send($emprunteur, new PortionJournaliere($projet, $portionCapital, $portionInteret));
                        Log::info('Notification envoyée pour le crédit ID : ' . $projet->id);

                        // Après l'envoi de la notification
                        event(new NotificationSent($emprunteur));
                        event(new PortionUpdated($projet->id, $projet->emprunteur_id, $projet->montan_restantt));
                    } elseif ($wallet->balance < $montantTotal) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($projet->emprunteur_id);
                        Log::info('emprunteur ID : ' . $projet->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $projet->id);
                        }
                        $message = 'Le solde de votre compte est insuffisant. Veuillez recharger votre compte pour effectuer cette opération.';
                        Notification::send($emprunteur, new PortionJournaliere($projet, $portionCapital, $portionInteret, $message));

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
        $transaction->projet_accord_id = $creditId;
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

<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\credits;
use App\Models\Crp;
use App\Models\portions_journalieres;
use App\Models\remboursements;
use App\Models\Transaction;
use App\Models\transactions_remboursement;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PortionJournaliere;
use App\Notifications\remboursement;
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
            if ($dateDuJour == $credit->date_debut || $dateDuJour <= $credit->date_fin) {
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

                    if ($balanceSuffisante) {
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

                            // Récupérer l'attribut investisseurs
                            $investisseurs = $credit->investisseurs;

                            // Initialiser un tableau pour stocker les IDs des investisseurs
                            $investisseursIds = [];

                            // Vérifier si investisseurs est un tableau simple
                            if (is_array($investisseurs)) {
                                $investisseursIds = $investisseurs;
                            } elseif (is_string($investisseurs)) {
                                // Si c'est une chaîne JSON, on la décode
                                $investisseursIds = json_decode($investisseurs, true);

                                // Vérifier que le décodage a bien fonctionné
                                if (!is_array($investisseursIds)) {
                                    Log::error('Échec du décodage JSON de l\'attribut investisseurs.');
                                    $investisseursIds = [];
                                }
                            } else {
                                Log::error('Les investisseurs ne sont ni un tableau, ni une chaîne JSON.');
                            }



                            // Log des IDs des investisseurs
                            Log::info('Liste des IDs des investisseurs : ' . implode(', ', $investisseursIds));

                            // Log des montants financés par investisseur
                            DB::beginTransaction();
                            try {
                                foreach ($investisseursIds as $id) {
                                    // Log de l'opération
                                    Log::info("Investisseur ID $id a financé : $credit->montant");

                                    // Mise à jour de la table CRP
                                    $crp = Crp::where('id_wallet', $wallet->id)->first();
                                    if ($crp) {
                                        // Vérifie si le solde est suffisant
                                        if ($crp->Solde >= $credit->montant) {
                                            $ancienSoldeCrp = $crp->Solde;
                                            $crp->Solde -= $credit->montant;
                                            $crp->save();

                                            // Log de la mise à jour
                                            Log::info('Mise à jour de la table CRP', [
                                                'id_wallet' => $wallet->id,
                                                'ancien_solde' => $ancienSoldeCrp,
                                                'nouveau_solde' => $crp->Solde,
                                                'montant_débité' => $credit->montant
                                            ]);
                                        } else {
                                            $emprunteur = User::find($credit->emprunteur_id);
                                            Log::info('emprunteur ID : ' . $id);
                                            if (!$emprunteur) {
                                                throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                                            }
                                            $message = 'Le solde de votre compte est insuffisant. Veuillez recharger votre compte pour effectuer cette opération.';

                                            Notification::send($emprunteur, new PortionJournaliere($credit, $emprunteur, $emprunteur, $message));

                                            // Log si le solde est insuffisant
                                            Log::warning('Solde insuffisant dans CRP pour effectuer la déduction', [
                                                'id_wallet' => $wallet->id,
                                                'solde_actuel' => $crp->Solde,
                                                'montant_requis' => $credit->montant
                                            ]);
                                            // Optionnel : Lever une exception ou retourner une erreur
                                            throw new Exception("Solde insuffisant pour effectuer cette opération.");
                                        }
                                    } else {
                                        Log::warning('Aucun enregistrement trouvé dans CRP pour id_wallet', [
                                            'id_wallet' => $wallet->id
                                        ]);
                                    }
                                    // Log de la mise à jour
                                    Log::info('Mise à jour de la table CRP', [
                                        'id_wallet' => $wallet->id,
                                        'nouveau_solde' => $crp->Solde,
                                        'montant_débité' => $credit->montant
                                    ]);

                                    $walletInvestisseurs = Wallet::where('user_id', $id)->first();
                                    Log::info('wallet de l\'investisseur', [
                                        'id_wallet' => $walletInvestisseurs->id,

                                    ]);

                                    // Mise à jour de la table COI
                                    $coi = Coi::where('id_wallet', $walletInvestisseurs->id)->first();
                                    if ($coi) {
                                        $coi->Solde += $credit->montant;
                                        $coi->save();
                                    }
                                    // Log de la mise à jour
                                    Log::info('Mise à jour de la table CRP', [
                                        'id_wallet' => $walletInvestisseurs->id,
                                        'nouveau_solde' => $coi->Solde,
                                        'montant_débité' => $credit->montant
                                    ]);

                                    $reference_id = $this->generateIntegerReference();
                                    $this->createTransaction(
                                        $credit->emprunteur_id,
                                        $id,
                                        'Envoie',
                                        $credit->montant,
                                        $reference_id,
                                        'Remboursement de financement',
                                        'effectué',
                                        $crp->type_compte
                                    );

                                    $this->createTransaction(
                                        $credit->emprunteur_id,
                                        $id,
                                        'Réception',
                                        $credit->montant,
                                        $reference_id,
                                        'Remboursement de financement',
                                        'effectué',
                                        $coi->type_compte
                                    );

                                    // Vous pourriez ajouter ici la logique d'envoi
                                    // Récupérer l'emprunteur associé au crédit
                                    $investisseur = User::find($id);
                                    Log::info('emprunteur ID : ' . $id);
                                    if (!$investisseur) {
                                        throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $credit->id);
                                    }

                                    $message = 'Payement de credit effectué avec success.';
                                    Notification::send($investisseur, new remboursement($message));
                                }
                                DB::commit();
                            } catch (Exception $e) {
                                // Annulation de toutes les modifications en cas d'erreur
                                DB::rollback();
                                Log::error("Erreur lors du traitement des transactions: " . $e->getMessage());
                                throw $e;
                            }
                            $credit->statut = "payé";

                            // 2. Récupérer les remboursements associés
                            $remboursements = remboursements::where('credit_id', $credit->id);

                            // 3. Vérifier si le montant remboursé est égal au montant total du crédit
                            if ($remboursements) {
                                // Mettre à jour le statut en "remboursé"
                                $remboursements->statut = 'remboursé';
                                $remboursements->save();
                            }
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
    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->type_compte = $type_compte;
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

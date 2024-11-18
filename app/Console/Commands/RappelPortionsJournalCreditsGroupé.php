<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\credits;
use App\Models\credits_groupé;
use App\Models\Crp;
use App\Models\portions_journalieres;
use App\Models\projets_accordé;
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

class RappelPortionsJournalCreditsGroupé extends Command
{

    protected $signature = 'app:rappel-journalieres-credits';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Date du jour
        $dateDuJour = Carbon::today()->toDateString();
        Log::info('Date du jour : ' . $dateDuJour);

        // Récupérer tous les crédits
        $credits = credits_groupé::where('statut', 'en cours')->get();
        Log::info('Nombre de credits avec statut "en cours" : ' . $credits->count());

        foreach ($credits as $credit) {
            Log::info('Credit ID : ' . $credit->id . ' - Emprunteur ID : ' . $credit->emprunteur_id);

            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour >= $credit->date_debut || $dateDuJour <= $credit->date_fin) {
                Log::info('Le crédit avec ID ' . $credit->id . ' est actif aujourd\'hui.');


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
                        $montantASoustraire = min($montantTotal, $credit->montan_restantt);

                        // Mettre à jour le solde du wallet et du CRP avec le montant ajusté
                        $wallet->balance -= $montantASoustraire;
                        $wallet->save();

                        if ($crp) {
                            $crp->Solde += $montantASoustraire;
                            $crp->save();
                        }

                        // Soustraire le montant ajusté du montant restant dans le crédit
                        $credit->montan_restantt -= $montantASoustraire;

                        // Vérifier si le crédit est totalement remboursé
                        if ($credit->montan_restantt == 0) {
                            $credit->statut = "remboursé";


                            $this->remboursementCredit($credit, $wallet,);
                        }


                        $credit->save();

                        $reference_id = $this->generateIntegerReference();

                        Log::info("Début de la transaction pour l'utilisateur ID: " . $credit->emprunteur_id);



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
                        $message = 'Le solde de votre compte est insuffisant. Penalité de 10%, Veuillez recharger votre compte pour effectuer cette opération.';
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


    protected function remboursementCredit($credit, $wallet)
    {
        // Décoder les investisseurs depuis le JSON
        $investisseursJson = $credit->investisseurs; // Chaîne JSON

        // Décodage du JSON en tableau PHP
        $decodedInvestisseurs = json_decode($investisseursJson, true);

        // Initialiser des tableaux pour stocker les IDs des investisseurs et leurs montants financés
        $investisseursIds = [];
        $investisseursMontants = [];

        if (is_array($decodedInvestisseurs)) {
            foreach ($decodedInvestisseurs as $investisseur) {
                // Vérifier que l'ID de l'investisseur et le montant financé sont définis, puis les ajouter aux tableaux
                if (isset($investisseur['investisseur_id'], $investisseur['montant_finance'])) {
                    $investisseursIds[] = $investisseur['investisseur_id'];

                    // Calcul du montant total financé avec les intérêts
                    $montantAvecInteret = $investisseur['montant_finance'] + ($investisseur['montant_finance'] * $credit->taux_interet) / 100;

                    // Stocker le montant financé augmenté des intérêts dans le tableau
                    $investisseursMontants[$investisseur['investisseur_id']] = $montantAvecInteret;
                }
            }
        }

        // Log des IDs des investisseurs
        Log::info('Liste des IDs des investisseurs : ' . implode(', ', $investisseursIds));

        DB::beginTransaction();
        try {
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
                    Log::info('emprunteur ID : ' . $emprunteur);
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
            foreach ($investisseursMontants as $id => $montant) {
                // Log de l'opération
                Log::info("Investisseur ID $id a financé : $montant");

                // Mise à jour de la table COI
                $walletInvestisseurs = Wallet::where('user_id', $id)->first();
                Log::info('wallet de l\'investisseur', [
                    'id_wallet' => $walletInvestisseurs->id,

                ]);

                // Mise à jour de la table COI
                $coi = Coi::where('id_wallet', $walletInvestisseurs->id)->first();
                if ($coi) {
                    $coi->Solde += $montant;
                    $coi->save();
                    // Log de la mise à jour
                    Log::info('Mise à jour de la table CRP', [
                        'id_wallet' => $walletInvestisseurs->id,
                        'nouveau_solde' => $coi->Solde,
                        'montant_débité' => $montant
                    ]);
                }

                $reference_id = $this->generateIntegerReference();
                $this->createTransaction(
                    $credit->emprunteur_id,
                    $id,
                    'Envoie',
                    $montant,
                    $reference_id,
                    'Remboursement de financement',
                    'effectué',
                    $crp->type_compte
                );

                $this->createTransaction(
                    $credit->emprunteur_id,
                    $id,
                    'Réception',
                    $montant,
                    $reference_id,
                    'Remboursement de financement',
                    'effectué',
                    $crp->type_compte
                );

                $credit->statut = "payé";

                // Envoi de la notification
                $investisseur = User::find($id);
                Log::info('Investisseur ID : ' . $id);
                if (!$investisseur) {
                    throw new Exception("Investisseur non trouvé pour le crédit ID : " . $credit->id);
                }

                $message = 'Paiement de crédit effectué avec succès.';
                Notification::send($investisseur, new remboursement($message));
            }
            DB::commit();
        } catch (Exception $e) {
            // Annulation de toutes les modifications en cas d'erreur
            DB::rollback();
            Log::error("Erreur lors du traitement des transactions: " . $e->getMessage());
            throw $e;
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
        $transaction->creditGrp_id = $creditId;
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

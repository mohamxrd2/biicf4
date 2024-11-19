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
        $projets = projets_accordé::where('statut', 'en cours')->orderBy('id')->get();
        Log::info('Nombre de projets avec statut "en cours" : ' . $projets->count());

        foreach ($projets as $projet) {
            Log::info('Projet ID : ' . $projet->id . ' - Emprunteur ID : ' . $projet->emprunteur_id);

            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour >= $projet->date_debut || $dateDuJour <= $projet->date_fin) {
                Log::info("Traitement du projet ID : " . $projet->id . '$projet->date_debut : ' . $projet->date_debut . ' - $projet->date_fin : ' . $projet->date_fin);


                $portionCapital = $projet->montant;
                $portionInteret = $projet->taux_interet;

                $montantTotal = $projet->portion_journaliere;

                Log::info("Montants récupérés pour le crédit ID: " . $projet->id, [
                    'portion_capital' => $portionCapital,
                    'portion_interet' => $portionInteret,
                    'montant_total' => $montantTotal
                ]);

                $wallet = Wallet::where('user_id', $projet->emprunteur_id)->first();
                $crp = Crp::where('id_wallet', $wallet->id)->first();

                if (!$wallet && !$crp) {
                    Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $projet->emprunteur_id);
                    continue;
                }

                DB::beginTransaction();
                try {
                    $balanceSuffisante = $wallet->balance >= $montantTotal;

                    if ($balanceSuffisante) {
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

                        if ($crp) {
                            $crp->Solde += $montantASoustraire;
                            $crp->save();
                        }

                        // Soustraire le montant ajusté du montant restant dans le crédit
                        $projet->montan_restantt -= $montantASoustraire;

                        // Vérifier si le crédit est totalement remboursé
                        if ($projet->montan_restantt == 0) {
                            $projet->statut = "remboursé";

                            $this->remboursementCredit($projet, $wallet);
                        }


                        $projet->save();


                        Log::info("Début de la transaction pour l'utilisateur ID: " . $projet->emprunteur_id);

                        $this->remboursement(
                            $projet->id,
                            $this->generateIntegerReference(),
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
                        $message = 'Le solde de votre compte est insuffisant. Penalité de 10%, Veuillez recharger votre compte pour effectuer cette opération.';
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

    protected function remboursementCredit($projet, $wallet)
    {
        // Décoder les investisseurs depuis le JSON
        $investisseursJson = $projet->investisseurs; // Chaîne JSON

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

                    $montantAvecInteret = $investisseur['montant_finance'] + ($investisseur['montant_finance'] * $projet->taux_interet) / 100;

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
                if ($crp->Solde >= $projet->montant) {
                    $ancienSoldeCrp = $crp->Solde;
                    $crp->Solde -= $projet->montant;
                    $crp->save();

                    // Log de la mise à jour
                    Log::info('Mise à jour de la table CRP', [
                        'id_wallet' => $wallet->id,
                        'ancien_solde' => $ancienSoldeCrp,
                        'nouveau_solde' => $crp->Solde,
                        'montant_débité' => $projet->montant
                    ]);
                } else {
                    $emprunteur = User::find($projet->emprunteur_id);
                    Log::info('emprunteur ID : ' . $emprunteur);
                    if (!$emprunteur) {
                        throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $projet->id);
                    }
                    $message = 'Le solde de votre compte est insuffisant. Veuillez recharger votre compte pour effectuer cette opération.';

                    Notification::send($emprunteur, new PortionJournaliere($projet, $emprunteur, $emprunteur, $message));

                    // Log si le solde est insuffisant
                    Log::warning('Solde insuffisant dans CRP pour effectuer la déduction', [
                        'id_wallet' => $wallet->id,
                        'solde_actuel' => $crp->Solde,
                        'montant_requis' => $projet->montant
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
                    $coi->Solde += $projet->montant;
                    $coi->save();
                    // Log de la mise à jour
                    Log::info('Mise à jour de la table CRP', [
                        'id_wallet' => $walletInvestisseurs->id,
                        'nouveau_solde' => $coi->Solde,
                        'montant_débité' => $projet->montant
                    ]);
                }

                $this->createTransaction(
                    $projet->emprunteur_id,
                    $id,
                    'Envoie',
                    $projet->montant,
                    $this->generateIntegerReference(),
                    'Remboursement de financement',
                    'effectué',
                    $crp->type_compte
                );

                $this->createTransaction(
                    $projet->emprunteur_id,
                    $id,
                    'Réception',
                    $projet->montant,
                    $this->generateIntegerReference(),
                    'Remboursement de financement',
                    'effectué',
                    $crp->type_compte
                );

                $projet->statut = "payé";

                // Envoi de la notification
                $investisseur = User::find($id);
                Log::info('Investisseur ID : ' . $id);
                if (!$investisseur) {
                    throw new Exception("Investisseur non trouvé pour le crédit ID : " . $projet->id);
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

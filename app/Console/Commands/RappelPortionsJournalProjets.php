<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\Admin;
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

        // Récupérer tous les crédits
        $projets = projets_accordé::where('statut', 'en cours')->orderBy('id')->get();

        foreach ($projets as $projet) {
            // Vérifier si la date du jour est entre la date de début et la date de fin du crédit
            if ($dateDuJour <= $projet->date_fin) {

                $portionCapital = $projet->montant;
                $portionInteret = $projet->taux_interet;

                $montantTotal = $projet->portion_journaliere;

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

                        // Après l'envoi de la notification
                        event(new NotificationSent($emprunteur));
                        event(new PortionUpdated($projet->id, $projet->emprunteur_id, $projet->montan_restantt));
                    } elseif ($wallet->balance < $montantTotal) {
                        // Récupérer l'emprunteur associé au crédit
                        $emprunteur = User::find($projet->emprunteur_id);
                        if (!$emprunteur) {
                            throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $projet->id);
                        }
                        $message = 'Le solde de votre compte est insuffisant. Penalité de 10%, Veuillez recharger votre compte pour effectuer cette opération.';
                        Notification::send($emprunteur, new PortionJournaliere($projet, $portionCapital, $portionInteret, $message));

                        // Après l'envoi de la notification
                        event(new NotificationSent($emprunteur));
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
                }
            } else {
                Log::warning('Aucun enregistrement trouvé dans CRP pour id_wallet', [
                    'id_wallet' => $wallet->id
                ]);
            }

            $montantTotalInvestisseurs = array_sum($investisseursMontants); // Total envoyé aux investisseurs

            foreach ($investisseursMontants as $id => $montant) {



                // Mise à jour de la table COI
                $walletInvestisseurs = Wallet::where('user_id', $id)->first();

                // Mise à jour de la table COI
                $coi = Coi::where('id_wallet', $walletInvestisseurs->id)->first();
                if ($coi) {
                    $coi->Solde += $montant;
                    $coi->save();
                }

                $this->createTransaction(
                    $projet->emprunteur_id,
                    $id,
                    'Envoie',
                    $montant,
                    $this->generateIntegerReference(),
                    'Remboursement de financement',
                    'effectué',
                    $crp->type_compte
                );

                $this->createTransaction(
                    $projet->emprunteur_id,
                    $id,
                    'Réception',
                    $montant,
                    $this->generateIntegerReference(),
                    'Remboursement de financement',
                    'effectué',
                    $coi->type_compte
                );

                $projet->statut = "payé";

                // Envoi de la notification
                $investisseur = User::find($id);

                if (!$investisseur) {
                    throw new Exception("Investisseur non trouvé pour le crédit ID : " . $projet->id);
                }

                $message = 'Paiement de crédit effectué avec succès.';
                Notification::send($investisseur, new remboursement($message));
            }

            // Calcul du montant restant
            $montantRestant = $projet->montant - $montantTotalInvestisseurs;
            // Vérifier si le montant restant est égal à la commission
            if ($montantRestant == $projet->comission) {
                // Récupérer l'ID de l'administrateur
                $admin = Admin::find(1); // Supposons que le rôle admin est défini
                if ($admin) {
                    $walletAdmin = Wallet::where('admin_id', $admin->id)->first();
                    if ($walletAdmin) {
                        // Ajouter le montant restant à l'administrateur
                        $walletAdmin->balance += $montantRestant;
                        $walletAdmin->save();

                        // Créer une transaction vers l'administrateur
                        $this->createTransactionAdmin(
                            $projet->emprunteur_id,
                            $admin->id,
                            'Envoie',
                            $montantRestant,
                            $this->generateIntegerReference(),
                            'Commision de la plateforme',
                            'effectué',
                            'COI'
                        );

                        // Créer une transaction vers l'administrateur
                        $this->createTransactionAdmin(
                            $projet->emprunteur_id,
                            $admin->id,
                            'Réception',
                            $montantRestant,
                            $this->generateIntegerReference(),
                            'Remboursement de financement',
                            'effectué',
                            'COI'
                        );
                    }
                }
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

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
                                        $investisseursMontants[$investisseur['investisseur_id']] = $investisseur['montant_finance'];
                                    }
                                }
                            }

                            // Log des IDs des investisseurs
                            Log::info('Liste des IDs des investisseurs : ' . implode(', ', $investisseursIds));

                            // Log des montants financés par investisseur
                            DB::beginTransaction();
                            try {
                                foreach ($investisseursMontants as $id => $montant) {
                                    // Log de l'opération
                                    Log::info("Investisseur ID $id a financé : $montant");

                                    // Mise à jour de la table CRP
                                    $crp = Crp::where('id_wallet', $wallet->id)->first();
                                    if ($crp) {
                                        $crp->Solde -= $montant;
                                        $crp->save();
                                    }

                                    // Mise à jour de la table COI
                                    $coi = Coi::where('id_wallet', $wallet->id)->first();
                                    if ($coi) {
                                        $coi->Solde += $montant;
                                        $coi->save();
                                    }

                                    $reference_id = $this->generateIntegerReference();
                                    $this->createTransaction(
                                        $projet->emprunteur_id,
                                        $id,
                                        'Envoi',
                                        $montant,
                                        $reference_id,
                                        'Remboursement de financement',
                                        'effectué',
                                        $crp->type_compte
                                    );

                                    $this->createTransaction(
                                        $projet->emprunteur_id,
                                        $id,
                                        'Réception',
                                        $montant,
                                        $reference_id,
                                        'Remboursement de financement',
                                        'effectué',
                                        $crp->type_compte
                                    );

                                    // Vous pourriez ajouter ici la logique d'envoi
                                    // Récupérer l'emprunteur associé au crédit
                                    $investisseur = User::find($id);
                                    Log::info('emprunteur ID : ' . $id);
                                    if (!$investisseur) {
                                        throw new Exception("Emprunteur non trouvé pour le crédit ID : " . $projet->id);
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

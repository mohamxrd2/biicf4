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

class RemboursementCredit extends Command
{

    protected $signature = 'app:RemboursementCredit';
    protected $description = 'Récupérer les portions journalières à rembourser depuis PROMIR';


    public function handle()
    {
        // Récupérer tous les crédits
        $projets = projets_accordé::where('statut', 'remboursé')->get();
        Log::info('Nombre de projets avec statut "remboursé" : ' . $projets->count());

        foreach ($projets as $projet) {
            Log::info('Projet ID : ' . $projet->id . ' - Emprunteur ID : ' . $projet->emprunteur_id);







            // Récupérer le wallet de l'utilisateur
            $wallet = Wallet::where('user_id', $projet->emprunteur_id)->first();
            if (!$wallet) {
                Log::warning("Wallet non trouvé pour l'emprunteur ID : " . $projet->emprunteur_id);
                continue;
            }

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


    protected function generateIntegerReference(): int
    {
        return (int) (now()->getTimestamp() * 1000 + now()->micro);
    }
}

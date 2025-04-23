<?php

namespace App\Jobs;


use App\Models\Crp;
use App\Models\Promir;
use App\Models\User;
use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionService;
use Illuminate\Queue\SerializesModels;

use Illuminate\Queue\InteractsWithQueue;
use App\Notifications\PortionJournaliere;
use App\Services\generateIntegerReference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Notification;

class Provision implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $userId;


    public function __construct($userId,)
    {
        $this->userId = $userId;
    }



    public function handle(): void
    {
        try {
            Log::info("🔄 [Provision] Début du traitement pour user_id={$this->userId}");

            // Étape 1 : Vérification de la liaison
            $liaison = Promir::where('user_id', $this->userId)->first();
            if (!$liaison) {
                Log::warning("⚠️ [Provision] Liaison non trouvée pour user_id={$this->userId}");
                return;
            }

            $systemeId = $liaison->system_client_id;
            Log::info("✅ [Provision] Liaison trouvée. system_client_id={$systemeId}");

            // Étape 2 : Appel API
            $client = new Client();
            $url = "https://promi.toopartoo.com/api/provision/{$systemeId}";
            Log::info("🌐 [Provision] Requête GET vers URL: {$url}");

            $response = $client->get($url, ['timeout' => 10]);
            $body = $response->getBody()->getContents();
            Log::info("📥 [Provision] Réponse API : " . $body);

            $data = json_decode($body, true);
            if (!isset($data['revenu_alloue'])) {
                Log::error("❌ [Provision] Clé 'revenu_alloue' manquante dans la réponse API");
                return;
            }

            $revenu_alloue = floatval($data['revenu_alloue']);
            Log::info("💰 [Provision] Revenu alloué : {$revenu_alloue}");

            if ($revenu_alloue == 0) {
                Log::info("ℹ️ [Provision] Revenu alloué = 0. Aucune action nécessaire.");
                return;
            }

            $emprunteur = User::findOrFail($this->userId);
            $wallet = Wallet::where('user_id', $this->userId)->first();

            if (!$wallet) {
                Log::warning("⚠️ [Provision] Wallet introuvable pour user_id={$this->userId}");
                return;
            }

            Log::info("👛 [Provision] Wallet trouvé. Solde actuel = {$wallet->balance}");

            // Génération de la référence pour toutes les transactions (succès ou échec)
            $reference_id = (new generateIntegerReference())->generate();
            Log::info("🔑 [Provision] Référence générée : {$reference_id}");

            $transactionService = new TransactionService();

            if ($wallet->balance >= $revenu_alloue) {
                $ancien_solde = $wallet->balance;
                $wallet->balance -= $revenu_alloue;
                $wallet->save();
                Log::info("✅ [Provision] Wallet mis à jour. Ancien solde = {$ancien_solde}, Nouveau solde = {$wallet->balance}");

                // Enregistrement de la transaction d'envoi
                $transactionService->createTransaction(
                    $this->userId,
                    $this->userId,
                    'Envoie',
                    $revenu_alloue,
                    $reference_id,
                    'Envoie au crp et cedd',
                    'COC'
                );
                Log::info("📤 [Provision] Transaction d'envoi enregistrée. Montant = {$revenu_alloue}");

                // Étape 6 : Gestion de l'épargne
                $epargne = isset($data['epargne']) ? floatval($data['epargne']) : 0;
                $cedd = $wallet->cedd;

                if ($cedd && $epargne > 0) {
                    $solde_cedd_avant = $cedd->Solde;
                    $cedd->Solde += $epargne;
                    $cedd->save();
                    Log::info("🏦 [Provision] Épargne ajoutée au CEDD. Avant = {$solde_cedd_avant}, Après = {$cedd->Solde}");

                    $revenu_alloue -= $epargne;

                    $transactionService->createTransaction(
                        $this->userId,
                        $this->userId,
                        'Réception',
                        $epargne,
                        $reference_id,
                        'Réception du CEDD',
                        'CRP'
                    );
                    Log::info("📥 [Provision] Transaction de réception CEDD enregistrée. Montant = {$epargne}");
                } else {
                    Log::info("ℹ️ [Provision] Pas d'épargne ou CEDD non trouvé pour user_id={$this->userId}");
                }

                // Étape 7 : Mise à jour du CRP
                if ($revenu_alloue > 0) {
                    $crp = $wallet->crp;
                    if ($crp) {
                        $revenu_crp_avant = $crp->Solde;
                        $crp->Solde += $revenu_alloue;
                        $crp->save();
                        Log::info("📈 [Provision] CRP mis à jour. Avant = {$revenu_crp_avant}, Après = {$crp->Solde}");

                        $transactionService->createTransaction(
                            $this->userId,
                            $this->userId,
                            'Réception',
                            $revenu_alloue,
                            $reference_id,
                            'Réception du CRP',
                            'CRP'
                        );
                        Log::info("📥 [Provision] Transaction de réception CRP enregistrée. Montant = {$revenu_alloue}");
                    } else {
                        Log::warning("⚠️ [Provision] CRP introuvable pour user_id={$this->userId}");
                    }
                } else {
                    Log::info("ℹ️ [Provision] Aucun revenu restant à transférer au CRP");
                }
            } else {
                Log::warning("❌ [Provision] Solde insuffisant. Balance = {$wallet->balance}, Requis = {$revenu_alloue}");

                // Création d'une transaction d'échec pour tracer la tentative
                // $transactionService->createTransaction(
                //     $this->userId,
                //     $this->userId,
                //     'Échec',
                //     $revenu_alloue,
                //     $reference_id,
                //     'Tentative de provision échouée - Solde insuffisant',
                //     'COC'
                // );
                Log::info("📝 [Provision] Transaction d'échec enregistrée. Montant requis = {$revenu_alloue}");

                Notification::send($emprunteur, new PortionJournaliere(
                    'Échec de paiement',
                    'Votre solde est insuffisant, veuillez vous recharger pour récupérer les revenus alloués'
                ));
                Log::info("📩 [Provision] Notification d'échec envoyée à user_id={$this->userId}");
                return;
            }

            Log::info("✅ [Provision] Traitement terminé avec succès pour user_id={$this->userId}");
        } catch (\Exception $e) {
            Log::error("❌ [Provision] Erreur : " . $e->getMessage());
        }
    }
}

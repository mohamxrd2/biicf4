<?php

namespace App\Jobs;


use App\Models\Crp;
use App\Models\Promir;
use App\Models\Wallet;
use GuzzleHttp\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use App\Services\TransactionService;
use Illuminate\Queue\SerializesModels;

use Illuminate\Queue\InteractsWithQueue;
use App\Services\generateIntegerReference;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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
        Log::info("Début du traitement de provision pour l'utilisateur ID {$this->userId}");

        // Étape 1 : Vérification de la liaison
        $liaison_reussie = Promir::where('user_id', $this->userId)->first();
    
        if (!$liaison_reussie) {
            Log::warning("Liaison non trouvée pour l'utilisateur ID {$this->userId}");
            return;
        }
    
        $systemeId = $liaison_reussie->system_client_id;
        Log::info("Liaison réussie. system_client_id = {$systemeId}");
    
        // Étape 2 : Appel API
        $client = new Client();
        $url = "https://toopartoo.com/promi/public/api/provision/{$systemeId}";
        Log::info("Envoi de la requête GET à l'URL : {$url}");

        $response = $client->get($url, ['timeout' => 10]);
        $body = $response->getBody()->getContents();
        Log::info("Réponse brute reçue : " . $body);
    
        $data = json_decode($body, true);
    
        // Étape 3 : Vérification des données API
        if (!isset($data['revenu_alloue'])) {
            Log::error("Clé 'revenu_alloue' absente dans la réponse API pour l'utilisateur ID {$this->userId}");
            return;
        }

        $revenu_alloue = floatval($data['revenu_alloue']);
        Log::info("Revenu alloué récupéré depuis l'API : {$revenu_alloue}");

        if($revenu_alloue != 0){

            $wallet = Wallet::where('user_id', $this->userId)->first();
    
        if ($wallet) {
            $ancien_solde = $wallet->balance;
            $wallet->balance += $revenu_alloue;
            $wallet->save();
            Log::info("Wallet mis à jour : Ancien solde = {$ancien_solde}, Nouveau solde = {$wallet->balance}");
        } else {
            Log::warning("Aucun Wallet trouvé pour l'utilisateur ID {$this->userId}");
        }

        // Étape 5 : Génération de la référence
        $reference_service = new generateIntegerReference();
        $reference_id = $reference_service->generate();
        Log::info("Référence générée pour la transaction : {$reference_id}");
    
        // Étape 6 : Création des transactions
        $TransactionService = new TransactionService();
    
        $TransactionService->createTransaction(
            $this->userId,
            $this->userId,
            'Envoie',
            $revenu_alloue,
            $reference_id,
            'Envoie au crp',
            'COC'
        );
        Log::info("Transaction d'envoi enregistrée : montant = {$revenu_alloue}, type = Envoie");

        // Étape 7 : Mise à jour du CRP
        $crp = $wallet->crp;
        if ($crp) {
            $revenu_crp_avant = $crp->Solde;
            $crp->Solde += $revenu_alloue;
            $crp->save();
            Log::info("CRP mis à jour : Avant = {$revenu_crp_avant}, Après = {$crp->Solde}");
        } else {
            Log::warning("CRP introuvable pour l'utilisateur ID {$this->userId}");
        }

        $TransactionService->createTransaction(
            $this->userId,
            $this->userId,
            'Reception',
            $revenu_alloue,
            $reference_id,
            'Reception du crp',
            'CRP'
        );
        Log::info("Transaction de réception enregistrée : montant = {$revenu_alloue}, type = Reception");

        Log::info("✅ Provision terminée avec succès pour l'utilisateur ID {$this->userId}");

        }
    
        // Étape 4 : Mise à jour du Wallet
        

    } catch (\Exception $e) {
        Log::error("❌ Erreur lors du provision pour l'utilisateur ID {$this->userId} : " . $e->getMessage());
    }
}

    
}

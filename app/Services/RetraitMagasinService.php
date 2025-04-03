<?php

namespace App\Services;

use App\Models\Gelement;
use App\Models\Transaction;
use App\Services\CommissionService;
use App\Events\NotificationSent;
use App\Notifications\Confirmation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Exception;

class RetraitMagasinService
{
    protected $notification;
    protected $user;
    protected $fournisseur;
    protected $userWalletFournisseur;
    protected $achatdirect;
    protected $produit;
    protected $userId;
    protected $codeVerification;

    /**
     * Constructeur du service
     *
     * @param array $data Les données nécessaires pour le service
     */
    public function __construct($data)
    {
        $this->notification = $data['notification'] ?? null;
        $this->user = $data['user'] ?? null;
        $this->fournisseur = $data['fournisseur'] ?? null;
        $this->userWalletFournisseur = $data['userWalletFournisseur'] ?? null;
        $this->achatdirect = $data['achatdirect'] ?? null;
        $this->produit = $data['produit'] ?? null;
        $this->userId = $data['userId'] ?? null;
        $this->codeVerification = $data['codeVerification'] ?? null;
    }

    /**
     * Traite le retrait en magasin
     *
     * @return void
     */
    public function retraitMagasin()
    {
        $commissionService = new CommissionService();
        // Décoder `data_finance` pour éviter l'erreur
        $dataFinance = json_decode($this->achatdirect->data_finance, true) ?? [];
        // Calcul du montant requis avec une conversion en float
        $requiredAmount = floatval($dataFinance['prix_apres_comission']);


        // Vérification de l'existence de l'achat dans les transactions gelées
        $existingGelement = Gelement::where('reference_id', $this->notification->data['code_unique'])
            ->first();

        if (!$existingGelement || $existingGelement->amount < $requiredAmount) {

            session()->flash('error', 'Le montant requis est insuffisant dans les transactions gelées.');
            return;
        }

        DB::beginTransaction(); // Démarre une transaction pour garantir la cohérence
        try {

            // Retirer le montant du gel
            $existingGelement->status = 'OK';
            $existingGelement->amount -=  $dataFinance['montantTotal'];
            $existingGelement->save();

            // met a jour le portefeuille de l'Fournisseur
            $this->userWalletFournisseur->balance += $requiredAmount;
            $this->userWalletFournisseur->save();


            $TransactionService = new TransactionService();


            $TransactionService->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Envoie',
                $dataFinance['montantTotal'],
                $this->generateIntegerReference(),
                'Debité pour achat',
                'COC'
            );

            $TransactionService->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Réception',
                $requiredAmount,
                $this->generateIntegerReference(),
                'Réception pour achat',
                'COC'
            );

            // Calcul des commissions
            $commissions = $dataFinance['montantTotal'] - $requiredAmount;

            // Paiement des commissions aux parrains
            $commissionService->handleCommissions($commissions, $this->fournisseur->parrain);

            $montantExcédent = $existingGelement->amount;

            // Traitement de l'excédent
            if ($montantExcédent > 0) {
                $this->handleExcedent(
                    $existingGelement->wallet,
                    $existingGelement,
                    $montantExcédent,
                    $this->user
                );
            }

            // Préparer les données pour le fournisseur
            $dataFournisseur = [
                'code_unique' => $this->achatdirect->code_unique,
                'id' => $this->achatdirect->id ?? null,
                'idProd' => $this->produit->id ?? null,
                'title' => 'Commande récupérée avec succès',
                'description' => 'Votre commande a été récupérée avec succès. Merci de votre confiance !',
            ];

            if ($this->fournisseur) {
                Notification::send($this->fournisseur, new Confirmation($dataFournisseur));
                event(new NotificationSent($this->fournisseur));

                Log::info('Notification envoyée au fournisseur', ['fournisseurId' => $this->fournisseur->id, 'code' => $this->codeVerification]);
            }

            if ($this->userId) {
                Notification::send($this->userId, new Confirmation($dataFournisseur));
            } else {
                Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->achatdirect->id);
            }

            DB::commit(); // Valide la transaction
        } catch (Exception $e) {
            DB::rollBack(); // Annule les modifications en cas d'erreur
            Log::error('Erreur lors du traitement de la livraison.', [
                'message' => $e->getMessage(),
                'user_id' => $this->userWallet->user_id ?? 'undefined',
                'required_amount' => $requiredAmount
            ]);
            session()->flash('error', 'Une erreur s\'est produite lors du traitement de la livraison.');
        }
    }

    private function handleExcedent($userWallet, $existingGelement, $montantExcédent, $userId)
    {
        $userWallet->balance += $montantExcédent;
        $userWallet->save();

        $existingGelement->amount -= $montantExcédent;
        $existingGelement->save();

        $TransactionService = new TransactionService();

        $TransactionService->createTransaction(
            $userId,
            $userId,
            'Réception',
            $montantExcédent,
            $this->generateIntegerReference(),
            'Retour des fonds en plus',
            'COC'
        );
    }

    /**
     * Génère un identifiant de référence unique basé sur l'horodatage
     *
     * @return int
     */
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
}

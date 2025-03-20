<?php

namespace App\Services;

use App\Models\gelement;
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

        // Calcul du montant requis avec une réduction de 10% cest pour le retrait en magasin
        $requiredAmount = floatval($this->notification->data['prixFin']);

        // Vérification de l'existence de l'achat dans les transactions gelées
        $existingGelement = gelement::where('reference_id', $this->notification->data['code_unique'])
            ->first();

        if (!$existingGelement || $existingGelement->amount < $requiredAmount) {
            Log::warning('Montant insuffisant ou aucune transaction gelée trouvée.', [
                'reference_id' => $this->notification->data['code_unique'],
                'required_amount' => $requiredAmount,
                'available_amount' => $existingGelement->amount ?? 0,
            ]);
            session()->flash('error', 'Le montant requis est insuffisant dans les transactions gelées.');
            return;
        }

        DB::beginTransaction(); // Démarre une transaction pour garantir la cohérence
        try {

            // Retirer le montant du gel
            $existingGelement->amount -= $requiredAmount;
            $existingGelement->status = 'OK';
            $existingGelement->save();

            // met a jour le portefeuille de l'Fournisseur
            $this->userWalletFournisseur->balance += $requiredAmount;
            $this->userWalletFournisseur->save();

            $this->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Envoie',
                $this->achatdirect->montantTotal,
                $this->generateIntegerReference(),
                'Debité pour achat',
                'effectué',
                'COC'
            );
            $this->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Réception',
                $requiredAmount,
                $this->generateIntegerReference(),
                'Réception pour achat',
                'effectué',
                'COC'
            );

            // Calcul des commissions
            $commissions = $this->achatdirect->montantTotal - $requiredAmount;

            // Paiement des commissions aux parrains
            $commissionService->handleCommissions($commissions, $this->fournisseur->parrain);

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

    /**
     * Crée une nouvelle transaction
     *
     * @param int $senderId ID de l'expéditeur
     * @param int $receiverId ID du destinataire
     * @param string $type Type de transaction
     * @param float $amount Montant de la transaction
     * @param int $reference_id ID de référence
     * @param string $description Description de la transaction
     * @param string $status Statut de la transaction
     * @param string $type_compte Type de compte
     *
     * @return void
     */
    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = $status;
        $transaction->save();
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

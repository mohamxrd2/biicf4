<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\VerifUser;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AchatDirectService
{
    private function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    private function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte)
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

   
    public function handlePourLivraison($data)
    {
        $achatdirect = $data['achatdirect'];
        $userWallet = $data['userWallet'];
        $notification = $data['notification'];
        $fournisseur = $data['fournisseur'];
        $userId = $data['userId'];
        $requiredAmount = $data['requiredAmount'];

        // Vérification des fonds
        if ($userWallet->balance < $requiredAmount) {
            throw new Exception('Fonds insuffisants dans votre portefeuille pour effectuer cette transaction.');
        }

        // Vérification de l'existence du gel
        $existingGelement = gelement::where('reference_id', $achatdirect->code_unique)
            ->where('id_wallet', $userWallet->id)
            ->first();

        if (!$existingGelement) {
            throw new Exception('Aucune transaction gelée existante trouvée.');
        }

        DB::beginTransaction();
        try {
            // Débit du portefeuille
            $userWallet->balance -= $requiredAmount;
            $userWallet->save();

            // Calcul des montants
            $totalMontantRequis = $achatdirect->montantTotal + $notification->data['prixTrade'];
            $montantExcédent = $existingGelement->amount + $requiredAmount - $totalMontantRequis;

            // Mise à jour du montant gelé
            $existingGelement->amount += $requiredAmount;
            $existingGelement->save();

            // Traitement de l'excédent
            if ($montantExcédent > 0) {
                $this->handleExcedent($userWallet, $existingGelement, $montantExcédent, $userId);
            }

            // Transaction pour la livraison
            $this->createTransaction(
                $userId,
                $userId,
                'Gele',
                $requiredAmount,
                $this->generateIntegerReference(),
                'Montant gelé pour la livraison',
                'effectué',
                'COC'
            );

            // Générer le code de vérification et notifier
            $codeVerification = random_int(1000, 9999);
            $achatdirect->update([
                'code_verification' => $codeVerification,
            ]);

            if ($fournisseur) {
                $this->notifyFournisseur($fournisseur, $achatdirect, $codeVerification);
            }

            // Mettre à jour la notification
            $notification->update(['reponse' => 'accepter']);

            DB::commit();
            return ['success' => true, 'message' => 'Validation effectuée avec succès.'];
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du traitement de la livraison.', [
                'message' => $e->getMessage(),
                'user_id' => $userWallet->user_id,
                'required_amount' => $requiredAmount
            ]);
            throw $e;
        }
    }

    private function handleExcedent($userWallet, $existingGelement, $montantExcédent, $userId)
    {
        $userWallet->balance += $montantExcédent;
        $userWallet->save();

        $existingGelement->amount -= $montantExcédent;
        $existingGelement->save();

        $this->createTransaction(
            $userId,
            $userId,
            'Réception',
            $montantExcédent,
            $this->generateIntegerReference(),
            'Retour des fonds en plus',
            'effectué',
            'COC'
        );

        Log::info('Excédent traité', [
            'user_id' => $userWallet->user_id,
            'montant' => $montantExcédent
        ]);
    }

    private function notifyFournisseur($fournisseur, $achatdirect, $codeVerification)
    {
        $dataFournisseur = [
            'code_unique' => $achatdirect->code_unique,
            'CodeVerification' => $codeVerification,
            'client' => $achatdirect->userSender,
            'id_achat' => $achatdirect->id,
        ];

        Notification::send($fournisseur, new VerifUser($dataFournisseur));
        event(new NotificationSent($fournisseur));
    }
}

<?php

namespace App\Services;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\AchatBiicf;
use App\Notifications\Confirmation;
use App\Notifications\VerifUser;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
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
    }

    private function notifyFournisseur($fournisseur, $achatdirect, $codeVerification)
    {
        $dataFournisseur = [
            'code_unique' => $achatdirect->code_unique,
            'CodeVerification' => $codeVerification,
            'client' => $achatdirect->userSender,
            'id_achat' => $achatdirect->id,
        ];

        if ($achatdirect->type_achat == 'OffreGrouper') {
            $fourniseur_Ids = userquantites::where('code_unique', $achatdirect->code_unique)
                ->pluck('user_id');
            foreach ($fourniseur_Ids as $id) {
                $fournisseurId = User::find($id);
                Notification::send($fournisseurId, new VerifUser($dataFournisseur));
                event(new NotificationSent($fournisseurId));

            }
        } else {
            Notification::send($fournisseur, new VerifUser($dataFournisseur));
            event(new NotificationSent($fournisseur));
        }
    }

    public function updatedQuantité($component)
    {
        $component->totalCost = (int)$component->quantité * $component->prix;

        if ($component->type === 'Produit') {
            $qteMin = $component->produit->qteProd_min;
            $qteMax = $component->produit->qteProd_max;

            if ($component->quantité < $qteMin || $component->quantité > $qteMax) {
                $component->errorMessage = "La quantité doit être comprise entre {$qteMin} et {$qteMax}.";
                $component->isButtonHidden = false;
                $component->isButtonDisabled = true;
                return;
            }
        }

        if ($component->totalCost > $component->userBalance->balance) {
            $solde = $component->userBalance->balance;
            $component->errorMessage = "Vous n'avez pas assez de fonds pour procéder. Votre solde est : {$solde} FCFA.";
            $component->isButtonHidden = true;
            $component->isButtonDisabled = true;
        } else {
            $component->errorMessage = '';
            $component->isButtonHidden = false;
            $component->isButtonDisabled = false;
        }
    }

    public function AchatDirectForm($component)
    {
        $validated = $component->validateData();

        if ($validated === false) {
            return;
        }

        $userId = Auth::id();
        if (!$userId) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $montantTotal = $component->totalCost;
        $userWallet = $this->getUserWallet($userId);

        $component->updatedQuantité();
        if (empty($component->selectedOption)) {
            $component->addError('selectedOption', 'Vous devez sélectionner une option de réception.');
        }

        DB::beginTransaction();
        try {
            $codeUnique = $this->generateUniqueReference();
            if (!$codeUnique) {
                throw new \Exception('Code unique non généré.');
            }

            $achat = $this->createPurchase($validated, $montantTotal, $codeUnique, $component->produit);

            gelement::create([
                'reference_id' => $codeUnique,
                'id_wallet' => $userWallet->id,
                'amount' => $montantTotal,
            ]);

            $this->updateWalletBalance($userWallet, $montantTotal);

            $reference_id = $this->generateIntegerReference();
            $description = $component->type === 'Produit'
                ? 'Gele Pour Achat de ' . $validated['nameProd']
                : 'Gele Pour Service de ' . $validated['nameProd'];

            $this->createTransaction(
                $userId,
                $validated['userTrader'],
                'Gele',
                $montantTotal,
                $reference_id,
                $description,
                'effectué',
                'COC'
            );

            $this->sendNotifications($validated, $achat, $codeUnique);

            DB::commit();

            $component->reset(['quantité', 'localite', 'dateTot', 'dateTard', 'timeStart', 'timeEnd', 'dayPeriod', 'dayPeriodFin']);

            $component->dispatch('formSubmitted', 'Achat Affectué Avec Succès');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue.');
        }
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6));
    }

    private function getUserWallet($userId)
    {
        return Wallet::where('user_id', $userId)->first();
    }

    private function updateWalletBalance($userWallet, $montantTotal)
    {
        $userWallet->decrement('balance', $montantTotal);
    }

    private function createPurchase($validated, $montantTotal, $codeUnique, $produit)
    {
        return AchatDirect::create([
            'nameProd' => $validated['nameProd'],
            'quantité' => $validated['quantité'],
            'montantTotal' => $montantTotal,
            'type_achat' => 'achatDirect',
            'localite' => $validated['localite'],
            'date_tot' => $validated['dateTot'] ?? null,
            'date_tard' => $validated['dateTard'] ?? null,
            'timeStart' => $validated['timeStart'] ?? null,
            'timeEnd' => $validated['timeEnd'] ?? null,
            'dayPeriod' => $validated['dayPeriod'] ?? null,
            'dayPeriodFin' => $validated['dayPeriodFin'] ?? null,
            'userTrader' => $validated['userTrader'],
            'userSender' => $validated['userSender'],
            'specificite' => $produit->specification,
            'photoProd' => $validated['photoProd'],
            'idProd' => $validated['idProd'],
            'code_unique' => $codeUnique,
        ]);
    }

    private function sendNotifications($validated, $achat, $codeUnique)
    {
        $userConnecte = User::find($validated['userSender']);
        Notification::send($userConnecte, new Confirmation([
            'nameProd' => $validated['nameProd'],
            'idProd' => $validated['idProd'],
            'code_unique' => $codeUnique,
            'idAchat' => $achat->id,
            'title' => 'Commande effectuée avec succès',
            'description' => 'Cliquez pour voir les détails de votre commande.',
        ]));

        $achatUser = [
            'nameProd' => $validated['nameProd'],
            'idProd' => $validated['idProd'],
            'type_achat' => $validated['selectedOption'],
            'code_unique' => $codeUnique,
            'idAchat' => $achat->id,
            'title' => 'Nouvelle commande',
            'description' => 'Veuillez vérifier si le produit est disponible.',
        ];

        $owner = User::find($validated['userTrader']);
        Notification::send($owner, new AchatBiicf($achatUser));
        event(new NotificationSent($owner));
    }
}

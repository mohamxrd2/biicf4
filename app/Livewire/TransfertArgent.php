<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\Transaction;
use App\Models\ComissionAdmin;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TransfertArgent extends Component
{
    public $search = '';
    public $users = [];
    public $user_id;
    public ?float $amount = null;


    public $errorMessage;

    // Méthode appelée lors de la mise à jour de la recherche
    public function mount()
    {
        $this->users = User::all();

        $this->resetForm(); // Réinitialiser les champs du formulaire par défaut
    }
    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Récupérer l'ID de l'utilisateur connecté
            $currentUserId = auth()->id();

            // Recherche des utilisateurs dont le nom d'utilisateur correspond à la saisie,
            // mais exclure l'utilisateur connecté
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId) // Exclure l'utilisateur connecté
                ->get();
        } else {
            // Si la barre de recherche est vide, ne rien afficher
            $this->users = [];
        }
    }

    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
    }

    public bool $isProcessing = false;

    public function submit()
    {
        // Vérifier si un traitement est déjà en cours
        if ($this->isProcessing) {
            return;
        }

        // Activer le mode "en traitement"
        $this->isProcessing = true;
        $this->errorMessage = '';

        // Validation des données
        $this->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:1|max:99999999',
        ], [
            'user_id.required' => 'Veuillez sélectionner un utilisateur.',
            'amount.required' => 'Veuillez entrer un montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'amount.max' => 'Le montant doit être inférieur à 100 millions.',
        ]);

        $senderId = Auth::id();
        $receiver = User::find($this->user_id);

        if (!$receiver) {
            Log::error('Utilisateur spécifié introuvable.', ['user_id' => $this->user_id]);
            $this->errorMessage = 'L\'utilisateur spécifié n\'existe pas.';
            $this->isProcessing = false; // Réinitialisation
            return;
        }

        $senderWallet = Wallet::where('user_id', $senderId)->first();
        $receiverWallet = Wallet::where('user_id', $receiver->id)->first();

        if (!$senderWallet || !$receiverWallet) {
            Log::error('Erreur lors de la récupération des portefeuilles.', [
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id
            ]);
            $this->errorMessage = 'Erreur lors de la récupération des portefeuilles.';
            $this->isProcessing = false; // Réinitialisation
            return;
        }

        if ($senderWallet->balance < $this->amount) {
            Log::warning('Solde insuffisant.', [
                'sender_balance' => $senderWallet->balance,
                'requested_amount' => $this->amount
            ]);
            $this->errorMessage = 'Solde insuffisant pour effectuer la recharge.';
            $this->isProcessing = false; // Réinitialisation
            return;
        }

        try {
            $commissions = round($this->amount * 0.01, 2);
            $totalDebit = $this->amount + $commissions;

            // Débiter et créditer les portefeuilles
            $senderWallet->decrement('balance', $totalDebit);
            $receiverWallet->increment('balance', $this->amount);

            // Générer la référence et enregistrer les transactions
            $referenceId = $this->generateIntegerReference();
            $this->createTransactionNew($senderId, $receiver->id, 'Réception', 'COC', $this->amount, $referenceId, 'Réception d\'argent');
            $this->createTransactionNew($senderId, $receiver->id, 'Envoie', 'COC', $totalDebit, $referenceId, 'Envoi d\'argent avec frais');

            // Gestion des commissions
            $this->handleCommissions($senderId, $receiver, $commissions, $referenceId);

            // Succès
            $this->dispatch('formSubmitted', 'Transfert effectué avec succès.');
            $this->resetForm();
            return redirect()->to(request()->header('Referer'));
        } catch (\Exception $e) {
            Log::error('Erreur lors du transfert.', [
                'exception' => $e->getMessage(),
                'sender_id' => $senderId,
                'receiver_id' => $receiver->id,
                'amount' => $this->amount
            ]);
            $this->errorMessage = 'Une erreur est survenue lors du transfert.';
        } finally {
            $this->isProcessing = false; // Toujours réinitialiser à la fin
        }
    }


    /**
     * Gérer les commissions pour les parrains et l'admin.
     */
    private function handleCommissions($senderId, $receiver, $commissions, $referenceId)
    {
        $remainingCommission = $commissions;

        // Commission pour le parrain de l'expéditeur
        $userSender = User::find($senderId);
        if ($userSender->parrain) {
            $parrainSender = User::find($userSender->parrain);
            if ($parrainSender) {
                $parrainSenderWallet = Wallet::where('user_id', $parrainSender->id)->first();
                if ($parrainSenderWallet) {
                    $parrainCommission = round($commissions * 0.01, 2);
                    $parrainSenderWallet->increment('balance', $parrainCommission);
                    $this->createTransactionNew($senderId, $parrainSender->id, 'Commission', 'COC', $parrainCommission, $referenceId, 'Commission pour le parrain');
                    $remainingCommission -= $parrainCommission;
                }
            }
        }

        // Commission pour le parrain du destinataire
        if ($receiver->parrain) {
            $parrainReceive = User::find($receiver->parrain);
            if ($parrainReceive) {
                $parrainReceiveWallet = Wallet::where('user_id', $parrainReceive->id)->first();
                if ($parrainReceiveWallet) {
                    $parrainCommission = round($commissions * 0.01, 2);
                    $parrainReceiveWallet->increment('balance', $parrainCommission);
                    $this->createTransactionNew($senderId, $parrainReceive->id, 'Commission', 'COC', $parrainCommission, $referenceId, 'Commission pour le parrain');
                    $remainingCommission -= $parrainCommission;
                }
            }
        }

        // Commission pour l'admin
        $adminWallet = ComissionAdmin::where('admin_id', 1)->first();
        if ($adminWallet) {
            $adminWallet->increment('balance', $remainingCommission);
            $this->createTransactionAdmin(
                $senderId,
                1,
                'Commission',
                $remainingCommission,
                $referenceId,
                'Commission de BICF',
                'effectué',
                'commission'
            );
        }
    }

    /**
     * Réinitialiser le formulaire après soumission.
     */
    private function resetForm()
    {
        $this->search = '';
        $this->reset(['user_id', 'amount']);
        $this->errorMessage = '';
    }


    protected function processTransaction($senderWallet, $receiverWallet)
    {
        $senderWallet->decrement('balance', $this->amount);
        $receiverWallet->increment('balance', $this->amount);
    }


    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
        $transaction->save();
    }
    protected function createTransactionAdmin(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_admin_id = $receiverId;
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
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }


    public function render()
    {
        return view('livewire.transfert-argent');
    }
}

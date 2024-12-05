<?php

namespace App\Livewire;

use App\Models\Coi;
use App\Models\RechargeSos;
use App\Models\User;
use App\Notifications\DepositRecu;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class DepositSos extends Component
{

    public function acceptDeposit()
    {
        try {
            // Marquer la notification comme acceptée
            Log::info("Début de l'acceptation de la demande avec ID notification : " . $this->notification->id);

            // Validation des données
            $this->validate([
                'amountDeposit' => 'required|numeric|min:1',
                'roiDeposit' => 'required|numeric|min:1',
                'operator' => 'required|string',
                'phonenumber' => 'required|numeric',
            ], [
                'amountDeposit.required' => 'Veuillez entrer un montant de dépôt.',
                'amountDeposit.numeric' => 'Le montant de dépôt doit être un nombre.',
                'amountDeposit.min' => 'Le montant de dépôt doit être supérieur à zéro.',
                'roiDeposit.required' => 'Veuillez entrer un ROI.',
                'roiDeposit.numeric' => 'Le ROI doit être un nombre.',
                'roiDeposit.min' => 'Le ROI doit être supérieur à zéro.',
                'operator.required' => 'Veuillez sélectionner un opérateur.',
                'operator.string' => 'L’opérateur doit être une chaîne de caractères.',
                'phonenumber.required' => 'Veuillez entrer un numéro de téléphone.',
                'phonenumber.numeric' => 'Le numéro de téléphone doit être un nombre.',
            ]);

            // Vérifier les valeurs après validation
            Log::info("Montant: $this->amountDeposit, ROI: $this->roiDeposit, Opérateur: $this->operator, Téléphone: $this->phonenumber");

            if ($this->existingRequest) {
                // Si la demande existe, afficher un message d'erreur
                session()->flash('error', 'La demande est expirée. Un utilisateur a déjà accepté la demande.');
                return; // Sortir de la méthode
            }

            // Insertion des données dans la table RechargeSos
            RechargeSos::create([
                'userdem' => $this->notification->data['user_id'],
                'userinvest' => Auth::id(),
                'montant' => $this->amountDeposit,
                'roi' => $this->roiDeposit,
                'operator' => $this->operator,
                'phone' => $this->phonenumber,
                'id_sos' => $this->id_sos,
                'statut' => 'accepté',
            ]);

            $investWallet = Wallet::where('user_id', Auth::id())->first();

            if (!$investWallet) {
                session()->flash('error', 'Wallet non trouvé pour l’utilisateur.');
                return;
            }

            $investCoi = Coi::where('id_wallet', $investWallet->id)->first();

            if (!$investCoi) {
                session()->flash('error', 'Coi non trouvé pour le wallet.');
                return;
            }

            $demWallet = Wallet::where('user_id', $this->notification->data['user_id'])->first();

            if (!$demWallet) {
                session()->flash('error', 'Wallet non trouvé pour l’utilisateur.');
                return;
            }

            $demCfa = Cfa::where('id_wallet', $demWallet->id)->first();

            if (!$demCfa) {
                session()->flash('error', 'Coi non trouvé pour le wallet.');
                return;
            }

            // Décrémenter le solde
            $investCoi->decrement('Solde', $this->amountDeposit);

            $demCfa->increment('Solde', $this->amountDeposit);

            // Générer un ID de référence
            $referenceId = $this->generateIntegerReference();

            // Créer la transaction
            $this->createTransactionNew(Auth::id(), Auth::id(), 'Gele', 'COI', $this->amountDeposit, $referenceId, 'Rechargement SOS');

            $this->createTransactionNew(Auth::id(), $this->notification->data['user_id'], 'Réception', 'CFA', $this->amountDeposit, $referenceId, 'Rechargement SOS');

            $data = [
                'user_id' => Auth::id(),
                'amount' => $this->amountDeposit,
                'roi' => $this->roiDeposit,
                'id_sos' => $this->id_sos,
                'phonenumber' => $this->phonenumber,
                'operator' => $this->operator,

            ];

            Notification::send(User::find($this->notification->data['user_id']), new DepositRecu($data));

            // Message de succès et réinitialisation du formulaire
            session()->flash('success', 'La demande de dépôt a été acceptée et enregistrée avec succès.');
            $this->resetForm();

            $this->notification->update(['reponse' => 'Accepté']);
        } catch (\Exception $e) {
            // Enregistrer l'erreur dans les logs
            Log::error('Erreur lors de l’acceptation de la demande de dépôt : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l’acceptation de la demande de dépôt : ' . $e->getMessage());
        }
    }

    public function rejectDeposit()
    {
        session()->flash('success', 'La demande de dépôt a été refusé et enregistrée avec succès.');
        $this->resetForm();

        $this->notification->update(['reponse' => 'Refusé']);
    }
    public function render()
    {
        return view('livewire.deposit-sos');
    }
}

<?php

namespace App\Livewire;

use App\Models\AjoutMontant;
use App\Models\CrediScore;
use App\Models\DemandeCredi;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class DetailsCredit extends Component
{
    public $id;
    public $notification;
    public $userDetails;
    public $demandeCredit;
    public $insuffisant = false;
    public $userInPromir;
    public $crediScore;
    public $solde;
    public $nombreInvestisseursDistinct = 0;
    public $sommeInvestie = 0;
    public $sommeRestante = 0;
    public $montant = ''; // Stocke le montant saisi

    public $pourcentageInvesti = 0;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Récupérer l'ID de l'utilisateur qui a demnder le credit depuis les données de la notification
        $userId = $this->notification->data['user_id'];
        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($userId);
        $userNumber = $this->userDetails->phone;

        // Récupérer l'ID de l'utilisateur connecté
        $user_connecte = Auth::id();
        $wallet = Wallet::where('user_id', $user_connecte)->first();
        $this->solde = $wallet ? $wallet->balance : 0;


        // Récupérer l'ID de la demande de credi du userId
        $demandeId = $this->notification->data['demande_id'];
        $this->demandeCredit = DemandeCredi::where('demande_id', $demandeId)->first();

        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', operator: $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');

        $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->sum('montant'); // Somme des montants investis



        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $this->userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($this->userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $this->crediScore = CrediScore::where('id_user', $this->userInPromir->id)->first();
        }


        // Calculer le pourcentage investi
        if ($this->demandeCredit->montant > 0) {
            $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
        }

        // Calculer la somme restante à investir
        $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie; // Montant total - Somme investie
         // Mettre à jour la notification et valider
         $this->notification->update(['reponse' => 'approved']);

    }
    public function updatedMontant()
    {
        // Vérifier si le montant saisi dépasse le solde
        $this->insuffisant = !empty($this->montant) && $this->montant > $this->solde;
    }
    public function confirmer()
    {
        // Vérifier que le montant est valide, non vide, numérique et supérieur à zéro
        $montant = floatval($this->montant); // Convertir le montant en float

        // if (empty($this->montant) || !is_numeric($montant) || $montant <= 0) {
        //     session()->flash('error', 'Veuillez saisir un montant valide.');
        //     return;
        // }

        // Récupérer le projet et le wallet de l'utilisateur (investisseur)
        $demandeCredit = $this->demandeCredit;

        // Vérifiez si le projet et le demandeur existent
        if (!$demandeCredit || !$this->notification->data['demande_id']) {
            session()->flash('error', 'La demande de credit ou le demandeur est introuvable.');
            return;
        }

        $wallet = Wallet::where('user_id', Auth::id())->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$wallet) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }

        // Vérifier que le solde du wallet est suffisant
        if ($wallet->balance < $montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }
        //Sauvegarder le montant dans la table `ajout_montant`
        try {
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant, // Utilisez la valeur float
                'id_invest' => Auth::id(),
                'id_emp' => $this->demandeCredit->id_user, // Vérifiez que cela n'est pas nul
                'id_demnd_credit' => $this->demandeCredit->id,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }
        // dd(vars: $ajoumontant);
        // // Mettre à jour le solde de l'utilisateur (investisseur)
        $wallet->balance -= $montant; // Utilisez la valeur float
        $wallet->save();

        // Message de succès
        session()->flash('success', 'Le montant a été ajouté avec succès.');

        // Réinitialiser le montant saisi et le drapeau de vérification de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;

        // Rafraîchir les propriétés du composant
        $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant'); // Met à jour la somme investie
        $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie; // Met à jour la somme restante
        $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100; // Met à jour le pourcentage investi

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');
    }
    public function approuver($montant)
    {
        // Convertir le montant en float
        $montant = floatval($montant);

        // Vérification si le montant est valide
        if ($montant <= 0) {
            session()->flash('error', 'Montant invalide.');
            return;
        }

        // Récupérer le projet et le wallet de l'utilisateur (investisseur)
        $demandeCredit = $this->demandeCredit;

        // Vérifiez si le projet et le demandeur existent
        if (!$demandeCredit || !$this->notification->data['demande_id']) {
            session()->flash('error', 'La demande de credit ou le demandeur est introuvable.');
            return;
        }

        $wallet = Wallet::where('user_id', Auth::id())->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$wallet) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }

        // Vérifier que le solde du wallet est suffisant
        if ($wallet->balance < $montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }
        //Sauvegarder le montant dans la table `ajout_montant`
        try {
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant, // Utilisez la valeur float
                'id_invest' => Auth::id(),
                'id_emp' => $this->demandeCredit->id_user, // Vérifiez que cela n'est pas nul
                'id_demnd_credit' => $this->demandeCredit->id,
            ]);
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }
        // dd(vars: $ajoumontant);
        // // Mettre à jour le solde de l'utilisateur (investisseur)
        $wallet->balance -= $montant; // Utilisez la valeur float
        $wallet->save();

        // Message de succès
        session()->flash('success', 'Le montant a été ajouté avec succès.');

        // Réinitialiser le montant saisi et le drapeau de vérification de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;

        // Rafraîchir les propriétés du composant
        $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant'); // Met à jour la somme investie
        $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie; // Met à jour la somme restante
        $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100; // Met à jour le pourcentage investi

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');
    }

    public function joursRestants()
    {
        $dateFin = \Carbon\Carbon::parse($this->demandeCredit->date_debut);
        $dateActuelle = now();
        $joursRestants = $dateActuelle->diffInDays($dateFin);
        return max(0, $joursRestants); // Retournez 0 si le projet est déjà terminé
    }
    public function render()
    {
        return view('livewire.details-credit', [
            'joursRestants' => $this->joursRestants(),
            'nombreInvestisseurs' => $this->nombreInvestisseursDistinct,
            'sommeRestante' => $this->sommeRestante,
            'pourcentageInvesti' => $this->pourcentageInvesti,
        ]);
    }
}

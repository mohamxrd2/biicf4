<?php

namespace App\Livewire;

use App\Models\AjoutMontant;
use App\Models\Cfa;
use App\Models\Countdown;
use App\Models\CrediScore;
use App\Models\DemandeCredi;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailsCredit extends Component
{
    public $id;
    public $notification;
    public $userId;
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
    public $montantVerifie = false;



    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Récupérer l'ID de l'utilisateur qui a demnder le credit depuis les données de la notification
        $this->userId = $this->notification->data['user_id'];
        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($this->userId);
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

        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant) // Assurez-vous que le champ 'montant' existe dans votre modèle
            ->exists(); // Renvoie true si le montant existe

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
        // Récupérer le wallet de l'utilisateur demandeur
        $walletDemandeur = Wallet::where('user_id', $this->userId)->first();

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

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {
            // Sauvegarder le montant dans la table `ajout_montant`
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->demandeCredit->id_user, // Assurez-vous que cet ID existe
                'id_demnd_credit' => $this->demandeCredit->id,
            ]);

            // // Mettre à jour le solde du wallet de l'investisseur
            // $wallet->balance -= $montant;
            // $wallet->save();

            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte
            if ($coi) {
                $coi->Solde -= $montant; // Débiter le montant du solde du COI
                $coi->save();
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // Mettre à jour ou créer un enregistrement dans la table CFA

            if ($cfa) {
                // Si le compte CFA existe, on additionne le montant
                $cfa->Solde += $montant; // Ajoute le montant au solde existant dans le CFA
                $cfa->save();
            }

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $reference_id,  'financement  de credit d\'achat',  'effectué');
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Reception', $montant, $reference_id,  'reception de financement  de credit d\'achat',  'effectué');

            // Committer la transaction
            DB::commit();

            // Message de succès
            session()->flash('success', 'Le montant a été ajouté avec succès.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

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

        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant) // Assurez-vous que le champ 'montant' existe dans votre modèle
            ->exists(); // Renvoie true si le montant existe
        if ($this->montantVerifie) {
            Countdown::create([
                'user_id' => Auth::id(), // Utilisez la valeur float
                'userSender' => $this->projet->demandeur->id,
                'start_time' => now(), // Vérifiez que cela n'est pas nul
                'code_unique' => $this->projet->id,
                'difference' => 'taux_projet',
            ]);
        }
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

        // Récupérer la demande de crédit et le wallet de l'utilisateur (investisseur)
        $demandeCredit = $this->demandeCredit;

        // Vérifiez si la demande de crédit et l'ID de la demande existent
        if (!$demandeCredit || !$this->notification->data['demande_id']) {
            session()->flash('error', 'La demande de crédit ou le demandeur est introuvable.');
            return;
        }

        // Récupérer le wallet de l'utilisateur connecté
        $wallet = Wallet::where('user_id', Auth::id())->first();
        // Récupérer le wallet de l'utilisateur demandeur
        $walletDemandeur = Wallet::where('user_id', $this->userId)->first();

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

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {

            // // Mettre à jour le solde du wallet de l'investisseur
            // $wallet->balance -= $montant;
            // $wallet->save();

            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte
            if ($coi) {
                $coi->Solde -= $montant; // Débiter le montant du solde du COI
                $coi->save();
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // Mettre à jour ou créer un enregistrement dans la table CFA

            if ($cfa) {
                // Si le compte CFA existe, on additionne le montant
                $cfa->Solde += $montant; // Ajoute le montant au solde existant dans le CFA
                $cfa->save();
            }

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $reference_id,  'financement  de credit d\'achat',  'effectué');
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Reception', $montant, $reference_id,  'reception de financement  de credit d\'achat',  'effectué');


            // Mettre à jour l'état de la notification en approuvé
            $this->notification->update(['reponse' => 'approved']);

            // Committer la transaction
            DB::commit();

            // Message de succès
            session()->flash('success', 'Le montant a été ajouté avec succès.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

        // Réinitialiser le montant saisi et le drapeau de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }




    public function refuser()
    {
        $this->notification->update(['reponse' => 'refuser']);
        session()->flash('error', 'Demande de credit refuser avec succes.');
    }
    public function joursRestants()
    {
        $dateFin = \Carbon\Carbon::parse($this->demandeCredit->date_fin);
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

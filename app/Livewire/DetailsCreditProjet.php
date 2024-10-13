<?php

namespace App\Livewire;
use App\Models\Cfa;
use App\Models\User;
use App\Models\Projet;
use App\Models\Wallet;
use App\Models\CrediScore;
use App\Models\UserPromir;
use App\Models\AjoutMontant;
use App\Models\DemandeCredi;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class DetailsCreditProjet extends Component
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

    public $projet;
    public $images = [];

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $userId = $this->notification->data['user_id'];

        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($userId);
        $userNumber = $this->userDetails->phone;

        // Récupérer l'ID de l'utilisateur connecté
        $user_connecte = Auth::id();
        $wallet = Wallet::where('user_id', $user_connecte)->first();
        $this->solde = $wallet ? $wallet->balance : 0;




        $projetId = $this->notification->data['projet_id'] ?? null;
        $this->projet = $projetId ? Projet::find($projetId) : null;


        if ($this->demandeCredit) {
            $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
                ->distinct()
                ->count('id_invest');
        } else {
            // Gérer le cas où $this->demandeCredit est nul
            $this->nombreInvestisseursDistinct = 0; // ou toute autre valeur par défaut
        }
        if ($this->demandeCredit) {
            $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
                ->sum('montant');
        } else {
            // Gérer le cas où $this->demandeCredit est nul
            $this->sommeInvestie = 0; // ou toute autre valeur par défaut
        }

        if ($this->projet) {
            $this->images = array_filter([
                $this->projet->photo1,
                $this->projet->photo2,
                $this->projet->photo3,
                $this->projet->photo4,
                $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
            ]);
        }





        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $this->userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($this->userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $this->crediScore = CrediScore::where('id_user', $this->userInPromir->id)->first();
        }
        if ($this->demandeCredit) {
            // Calculer le pourcentage investi
            if ($this->demandeCredit->montant > 0) {
                $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100; // Calculer le pourcentage investi
            } else {
                $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
            }
            // Calculer la somme restante à investir
            $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie; // Montant total - Somme investie
            // Mettre à jour la notification et valider

        }
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

            // Mettre à jour le solde du wallet de l'investisseur
            $wallet->balance -= $montant;
            $wallet->save();

            // Mettre à jour ou créer un enregistrement dans la table CFA
            Cfa::updateOrCreate(
                ['id_user' => $this->userDetails->id], // Assurez-vous de mettre à jour par l'ID de l'utilisateur
                [
                    'Solde' => $montant, // Ajout du montant à la somme existante
                    'Date_Creation' => now(),
                ]
            );

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

        // Récupérer la demande de crédit et le wallet de l'utilisateur (investisseur)
        // $demandeCredit = $this->demandeCredit;

        // // Vérifiez si la demande de crédit et l'ID de la demande existent
        // if (!$demandeCredit || !$this->notification->data['demande_id']) {
        //     session()->flash('error', 'La demande de crédit ou le demandeur est introuvable.');
        //     return;
        // }

        // Récupérer le wallet de l'utilisateur connecté
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

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {
            // Sauvegarder le montant dans la table `ajout_montant`
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->notification->data['user_id'], // Assurez-vous que cet ID existe
                'id_demnd_credit' => $this->demandeCredit->id ?? null,
                'id_projet' => $this->projet->id ?? null
            ]);

            // Mettre à jour le solde du wallet de l'investisseur
            $wallet->balance -= $montant;
            $wallet->save();

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant);

            // Mettre à jour ou créer un enregistrement dans la table CFA
            Cfa::updateOrCreate(
                ['id_user' => $this->notification->data['user_id']], // Assurez-vous de mettre à jour par l'ID de l'utilisateur
                [
                    'Solde' => $montant, // Ajout du montant à la somme existante
                    'Date_Creation' => now(),
                ]
            );

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

        // Rafraîchir les propriétés du composant
        if ($this->demandeCredit) {

            $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');
            $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie;

            // Assurez-vous que la division n'est pas par zéro
            if ($this->demandeCredit->montant > 0) {
                $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100;
            } else {
                $this->pourcentageInvesti = 0;
            }

            $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
                ->distinct()
                ->count('id_invest');
        } else {

            $this->sommeInvestie = AjoutMontant::where('id_projet', $this->notification->data['id_projet'])->sum('montant');
            $this->sommeRestante = $this->notification->data['montant'] - $this->sommeInvestie;

            // Assurez-vous que la division n'est pas par zéro
            if ($this->notification->data['montant'] > 0) {
                $this->pourcentageInvesti = ($this->sommeInvestie / $this->notification->data['montant']) * 100;
            } else {
                $this->pourcentageInvesti = 0;
            }

            $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->notification->data['id_projet'])
                ->distinct()
                ->count('id_invest');
        }





        // Mettre à jour le nombre d'investisseurs distincts


        // Réinitialiser le montant saisi et le drapeau de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;
    }


    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }


    public function refuser()
    {
        $this->notification->update(['reponse' => 'refuser']);
        session()->flash('error', 'Demande de credit refuser avec succes.');
    }
    public function joursRestants()
    {
        $dateFin = \Carbon\Carbon::parse($this->notification->data['duree']);
        $dateActuelle = now();
        $joursRestants = $dateActuelle->diffInDays($dateFin);
        return max(0, $joursRestants); // Retournez 0 si le projet est déjà terminé
    }

    public function render()
    {
        return view('livewire.details-credit-projet', [
            'joursRestants' => $this->joursRestants(),
            'nombreInvestisseurs' => $this->nombreInvestisseursDistinct,
            'sommeRestante' => $this->sommeRestante,
            'pourcentageInvesti' => $this->pourcentageInvesti,
            'images' => $this->images,
        ]);

    }
}
<?php

namespace App\Livewire;

use App\Models\Cfa;
use App\Models\projets_accordé;
use App\Models\User;
use App\Models\Projet;
use App\Models\Wallet;
use App\Models\CrediScore;
use App\Models\remboursements;
use App\Models\UserPromir;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;

class DetailsCreditProjet extends Component
{
    public $id;
    public $notification;
    public $userId;
    public $userDetails;
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
        $this->userId = $this->notification->data['user_id'] ?? $this->notification->data['id_emp'];

        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($this->userId);
        $userNumber = $this->userDetails->phone ?? null;

        // Récupérer l'ID de l'utilisateur connecté
        $user_connecte = Auth::id();
        $wallet = Wallet::where('user_id', $user_connecte)->first();
        $this->solde = $wallet ? $wallet->balance : 0;

        $projetId = $this->notification->data['projet_id'] ?? null;
        $this->projet = $projetId ? Projet::find($projetId) : null;

        $this->images = array_filter([
            $this->projet->photo1,
            $this->projet->photo2,
            $this->projet->photo3,
            $this->projet->photo4,
            $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
        ]);


        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $this->userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($this->userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $this->crediScore = CrediScore::where('id_user', $this->userInPromir->id)->first();
        }
    }
    public function updatedMontant()
    {
        // Vérifier si le montant saisi dépasse le solde
        $this->insuffisant = !empty($this->montant) && $this->montant > $this->solde;
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
        if ($wallet->coi->Solde < $montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {

            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte

            if ($coi) {
                // Vérifie si le solde est suffisant pour le débit
                if ($coi->Solde >= $montant) {
                    $coi->Solde -= $montant; // Débiter le montant du solde du COI
                    $coi->save();
                } else {
                    // Retourne un message ou gère le cas où le solde est insuffisant
                    session()->flash('error', 'Solde insuffisant dans le COI.');
                    // Arrête le processus si le solde est insuffisant
                    return;
                }
            } else {
                // Gérer le cas où le compte COI n'existe pas ou n'est pas trouvé
                session()->flash('error', 'Compte COI introuvable.');
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // Mettre à jour ou créer un enregistrement dans la table CFA
            if ($cfa) {
                // Si le compte CFA existe, on additionne le montant
                $cfa->Solde += $montant; // Ajoute le montant au solde existant dans le CFA
                $cfa->save();
            }

            //  Calculer la portion journalière en fonction du montant et de la durée.
            // Assurez-vous que les dates sont bien des instances de Carbon
            $debut = Carbon::parse($this->projet->date_fin);
            $durer = Carbon::parse($this->projet->durer);
            $jours = $debut->diffInDays($durer);

            $montantTotal = $montant * (1 + $this->projet->taux / 100);
            $portion_journaliere = $jours > 0 ? $montantTotal  / $jours : 0;

            $resultatsInvestisseurs = [];

            // Stocker dans le tableau les informations sur le projet, l'investisseur et le montant total financé
            $resultatsInvestisseurs[] = [
                'projet_id' => $this->projet->id,
                'investisseur_id' => Auth::id(),
                'montant_finance' => $montant, // Montant total financé par cet investisseur
            ];

            // Mettre à jour ou créer un enregistrement dans la table credits
            projets_accordé::create([
                'emprunteur_id' => $this->userId,
                'investisseurs' => json_encode($resultatsInvestisseurs),
                'montant' => $montantTotal,
                'montan_restantt' => $montantTotal,
                'taux_interet' => $this->projet->taux,
                'date_debut' => $this->projet->date_fin,
                'date_fin' => $this->projet->durer,
                'portion_journaliere' => $portion_journaliere,
                'statut' => 'en cours',
            ]);

            // Création du remboursement associé
            remboursements::create([
                'projet_id' => $this->projet->id,  // Associe le remboursement au crédit créé
                'id_user' => Auth::id(),  // Associe le remboursement au crédit créé
                'montant_capital' => $montant,  // Définissez cette variable en fonction de votre logique métier
                'montant_interet' => $this->projet->taux,  // Définissez cette variable en fonction de votre logique métier
                'date_remboursement' => $this->projet->durer,  // Définissez cette variable en fonction de votre logique métier
                'statut' => 'en cours',  // Statut du remboursement
                'description' => $this->projet->name,  // Statut du remboursement
            ]);


            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $montant, $this->generateIntegerReference(),  'financement  de credit d\'achat',  'effectué');
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Reception', $montant, $this->generateIntegerReference(),  'reception de financement  de credit d\'achat',  'effectué');

            // Mettre à jour l'état de la notification en approuvé
            $this->notification->update(['reponse' => 'approved']);
            $this->projet->update(['count' => true]);

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
        $dateFin = Carbon::parse($this->projet->date_fin);
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

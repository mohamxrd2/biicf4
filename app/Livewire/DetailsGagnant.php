<?php

namespace App\Livewire;

use App\Models\Cfa;
use App\Models\CrediScore;
use App\Models\credits;
use App\Models\DemandeCredi;
use App\Models\remboursements;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DetailsGagnant extends Component
{
    public $id;
    public $notification;
    public $demandeCredit;
    public $userId;
    public $userDetails;
    public $crediScore;
    public $userInPromir;
    public $pourcentageInvesti = 0;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Récupérer l'ID de l'utilisateur qui a demnder le credit depuis les données de la notification
        $this->userId = $this->notification->data['id_emp'];
        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($this->userId);
        $userNumber = $this->userDetails->phone;

        // Récupérer l'ID de l'utilisateur connecté
        $user_connecte = Auth::id();
        $wallet = Wallet::where('user_id', $user_connecte)->first();

        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $this->userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($this->userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $this->crediScore = CrediScore::where('id_user', $this->userInPromir->id)->first();
        }

        // Récupérer l'ID de la demande de credi du userId
        $credit_id = $this->notification->data['credit_id'];
        $this->demandeCredit = DemandeCredi::find($credit_id)->first();
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
        if (!$demandeCredit) {
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
            $debut = Carbon::parse($this->demandeCredit->date_fin);
            $durer = Carbon::parse($this->demandeCredit->duree);
            $jours = $debut->diffInDays($durer);

            $montantTotal = $montant * (1 + $demandeCredit->taux / 100);
            $portion_journaliere = $jours > 0 ? $montantTotal  / $jours : 0;

            // Mettre à jour ou créer un enregistrement dans la table credits
            $credit = credits::create([
                'emprunteur_id' => $this->userId,
                'investisseurs' => [Auth::id()],
                'montant' => $montantTotal,
                'montant_restant' => $montantTotal,
                'taux_interet' => $demandeCredit->taux,
                'date_debut' => $demandeCredit->date_debut,
                'date_fin' => $demandeCredit->duree,
                'portion_journaliere' => $portion_journaliere,
                'statut' => 'en_cours',
            ]);
            // Création du remboursement associé
            remboursements::create([
                'credit_id' => $credit->id,  // Associe le remboursement au crédit créé
                'id_user' => Auth::id(),  // Associe le remboursement au crédit créé
                'montant_capital' => $montant,  // Définissez cette variable en fonction de votre logique métier
                'montant_interet' => $demandeCredit->taux,  // Définissez cette variable en fonction de votre logique métier
                'date_remboursement' => $demandeCredit->duree,  // Définissez cette variable en fonction de votre logique métier
                'statut' => 'en cours',  // Statut du remboursement
                'description' => $demandeCredit->objet_financement,  // Statut du remboursement
            ]);

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $reference_id,  'Financement  de Crédit d\'achat',  'effectué', $coi->type_compte);
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Réception', $montant, $reference_id,  'Réception de Fonds  de Credit d\'achat',  'effectué', $cfa->type_compte);


            // Mettre à jour l'état de la notification en approuvé
            $this->notification->update(['reponse' => 'approved']);
            $this->demandeCredit->update(['status' => 'terminer']);

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
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
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

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }

    public function render()
    {
        return view('livewire.details-gagnant');
    }
}

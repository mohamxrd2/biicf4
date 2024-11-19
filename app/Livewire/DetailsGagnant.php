<?php

namespace App\Livewire;

use App\Models\AjoutAction;
use App\Models\Cfa;
use App\Models\Countdown;
use App\Models\CrediScore;
use App\Models\credits;
use App\Models\credits_groupé;
use App\Models\DemandeCredi;
use App\Models\gelement;
use App\Models\Projet;
use App\Models\projets_accordé;
use App\Models\remboursements;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class DetailsGagnant extends Component
{
    public $id;
    public $notification;
    public $demandeCredit;
    public $projet;
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

        // Récupérer l'ID soit du crédit soit du projet
        $credit_id = $this->notification->data['credit_id'] ?? null;
        $projet_id = $this->notification->data['projet_id'] ?? null;

        // Vérifier si c'est un crédit ou un projet
        if ($credit_id) {
            // Si c'est un crédit, récupérer la demande de crédit
            $this->demandeCredit = DemandeCredi::find($credit_id);
        } elseif ($projet_id) {
            // Si c'est un projet, récupérer les informations du projet
            $this->projet = Projet::find($projet_id);
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
        if (!$demandeCredit) {
            session()->flash('error', 'La demande de crédit ou le demandeur est introuvable.');
            return;
        }

        // Récupérer le wallet de l'utilisateur connecté
        $wallet = Wallet::where('user_id', Auth::id())->first();
        // Récupérer le wallet de l'utilisateur demandeur
        $walletDemandeur = Wallet::where('user_id', $this->userId)->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$wallet && !$walletDemandeur) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }



        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {

            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte

            // Vérifier si le id_wallet existe dans la table `gelements`
            $gelement = gelement::where('status', 'Pending')
                ->where('id_wallet', $wallet->id)
                ->where('reference_id', $this->notification->data['credit_id'])
                ->first();

            if (!$gelement) {
                throw new \Exception('Gélément introuvable.');
            }

            // Vérifier si le `reference_id` correspond au `code_unique` dans la table Countdown
            $countdown = Countdown::where('code_unique', $gelement->reference_id)->exists();
            if (!$countdown) {
                throw new \Exception('Référence invalide.');
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            if ($cfa) {
                $cfa->Solde += $montant;
                $cfa->save();
            } else {
                $cfa = Cfa::create([
                    'id_wallet' => $walletDemandeur->id,
                    'Solde' => $montant,
                ]);
            }

            //  Calculer la portion journalière en fonction du montant et de la durée.
            // Assurez-vous que les dates sont bien des instances de Carbon
            $debut = Carbon::parse($this->demandeCredit->date_fin);
            $durer = Carbon::parse($this->demandeCredit->duree);
            $jours = $debut->diffInDays($durer);

            $montantTotal = $montant * (1 + $demandeCredit->taux / 100);
            $portion_journaliere = $jours > 0 ? $montantTotal  / $jours : 0;

            $resultatsInvestisseurs = [
                [
                    'credit_id' => $this->demandeCredit->id,
                    'investisseur_id' => Auth::id(),
                    'montant_finance' => $montant,
                ],
            ];

            // Mettre à jour ou créer un enregistrement dans la table credits
            $creditGrp_id = credits_groupé::create([
                'emprunteur_id' => $this->userId,
                'investisseurs' => json_encode($resultatsInvestisseurs),
                'montant' => $montantTotal,
                'montan_restantt' => $montantTotal,
                'taux_interet' => $this->demandeCredit->taux,
                'date_debut' => $this->demandeCredit->date_fin,
                'date_fin' => $this->demandeCredit->duree,
                'portion_journaliere' => $portion_journaliere,
                'statut' => 'en cours',
                'description' => $this->demandeCredit->objet_financement,
            ]);

            // Création du remboursement associé
            Remboursements::create([
                'creditGrp_id' => $creditGrp_id->id,  // Associe le remboursement au crédit créé
                'id_user' => Auth::id(),  // Associe le remboursement au crédit créé
                'montant_capital' => $montant,  // Définissez cette variable en fonction de votre logique métier
                'montant_interet' => $this->demandeCredit->taux,  // Définissez cette variable en fonction de votre logique métier
                'date_remboursement' => $this->demandeCredit->duree,  // Définissez cette variable en fonction de votre logique métier
                'statut' => 'en cours',  // Statut du remboursement
                'description' => $this->demandeCredit->objet_financement,  // Statut du remboursement
            ]);

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $this->generateIntegerReference(),  'Financement  de Crédit d\'achat',  'effectué', $coi->type_compte);
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Réception', $montant, $this->generateIntegerReference(),  'Réception de Fonds  de Credit d\'achat',  'effectué', $cfa->type_compte);

            // Mettre à jour le champ 'count' à true pour toutes les demandes récupérées
            $this->demandeCredit->update([
                'status' => 'terminer'
            ]);

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
    }
    public function approuver2($montant)
    {
        // Convertir le montant en float
        $montant = floatval($montant);

        // Vérification si le montant est valide
        if ($montant <= 0) {
            session()->flash('error', 'Montant invalide.');
            return;
        }
        // Récupérer la demande de crédit et le wallet de l'utilisateur (investisseur)
        $projet = $this->projet;

        // Vérifiez si la demande de crédit et l'ID de la demande existent
        if (!$projet) {
            session()->flash('error', 'La demande de crédit ou le demandeur est introuvable.');
            return;
        }
        // Récupérer le wallet de l'utilisateur connecté
        $wallet = Wallet::where('user_id', Auth::id())->first();
        $walletDemandeur = Wallet::where('user_id', $this->userId)->first();

        if (!$wallet || !$walletDemandeur) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }


        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {

            // Vérifier que le solde du COI est suffisant
            $coi = $wallet->coi; // Assurez-vous que la relation `coi` est définie dans le modèle Wallet

            // Vérifier si le id_wallet existe dans la table `gelements`
            ($gelement = gelement::where('status', 'Pending')
                ->where('id_wallet', $wallet->id)
                ->where('reference_id', $this->notification->data['projet_id'])
                ->first());

            if (!$gelement) {
                throw new \Exception('Gélément introuvable.');
            }

            // Vérifier si le `reference_id` correspond au `code_unique` dans la table Countdown
            $countdown = Countdown::where('code_unique', $gelement->reference_id)->exists();
            if (!$countdown) {
                throw new \Exception('Référence invalide.');
            }

            // Mettre à jour ou créer un enregistrement dans la table CFA
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            if ($cfa) {
                $cfa->Solde += $montant;
                $cfa->save();
            } else {
                $cfa = Cfa::create([
                    'id_wallet' => $walletDemandeur->id,
                    'Solde' => $montant,
                ]);
            }

            // Calculer les données nécessaires pour le projet
            $debut = Carbon::parse($this->projet->date_fin);
            $fin = Carbon::parse($this->projet->durer);
            $jours = $debut->diffInDays($fin);

            $montantTotal = $montant * (1 + $this->projet->taux / 100);
            $portion_journaliere = $jours > 0 ? $montantTotal / $jours : 0;

            $resultatsInvestisseurs = [
                [
                    'projet_id' => $this->projet->id,
                    'investisseur_id' => Auth::id(),
                    'montant_finance' => $montant,
                ],
            ];

            // Vérifier les actions existantes
            $actions = AjoutAction::where('id_projet', $this->projet->id)
                ->select('id_invest', DB::raw('SUM(montant) as total_montant'), DB::raw('SUM(nombreActions) as nombre_actions'))
                ->groupBy('id_invest')
                ->get();

            $actionsData = [];
            foreach ($actions as $action) {
                $actionsData[] = [
                    'projet_id' => $this->projet->id,
                    'investisseur_id' => $action->id_invest,
                    'montant_finance' => $action->total_montant,
                    'nombreActions' => $action->nombre_actions,
                ];
            }

            // Créer un enregistrement dans la table projets_accordé
            projets_accordé::create([
                'emprunteur_id' => $this->userId,
                'investisseurs' => json_encode($resultatsInvestisseurs),
                'montant' => $montantTotal,
                'montan_restantt' => $montantTotal,
                'action' => json_encode($actionsData),
                'taux_interet' => $this->projet->taux,
                'date_debut' => $this->projet->date_fin,
                'date_fin' => $this->projet->durer,
                'portion_journaliere' => $portion_journaliere,
                'statut' => 'en cours',
            ]);

            // Créer un remboursement
            remboursements::create([
                'projet_id' => $this->projet->id,
                'id_user' => Auth::id(),
                'montant_capital' => $montant,
                'montant_interet' => $this->projet->taux,
                'date_remboursement' => $this->projet->durer,
                'statut' => 'en cours',
                'description' => $this->projet->name,
            ]);

            // Effectuer les transactions
            $reference_id = $this->generateIntegerReference();
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $montant, $reference_id, 'Financement de crédit', 'effectué', $coi->type_compte);
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Réception', $montant, $reference_id, 'Réception de financement', 'effectué', $cfa->type_compte);

            // Mettre à jour la notification et le projet
            $this->notification->update(['reponse' => 'approved']);
            $this->projet->update(['count' => true]);

            DB::commit();

            session()->flash('success', 'Le montant a été ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            Log::error('Erreur dans approuver2 : ' . $e->getMessage());
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

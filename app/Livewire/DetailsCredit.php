<?php

namespace App\Livewire;

use App\Events\AjoutMontantF;
use App\Events\CommentSubmittedTaux;
use App\Events\DebutDeNegociation;
use App\Models\AjoutMontant;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\CrediScore;
use App\Services\CreditApprovalService;
use App\Models\credits_groupé;
use App\Models\DemandeCredi;
use App\Models\gelement;
use App\Models\remboursements;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use App\Notifications\RefusAchat;
use App\Services\TauxSubmissionService;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Attributes\On;
use Livewire\Component;

class DetailsCredit extends Component
{
    public $id;
    public $notification, $userId, $userDetails, $demandeCredit, $insuffisant = false, $userInPromir,
        $crediScore, $solde, $nombreInvestisseursDistinct = 0, $sommeInvestie = 0, $investisseurQuiAPayeTout,
        $montantVerifie = false, $sommeRestante = 0, $montant = ''; // Stocke le montant saisi
    protected $listeners = ['compteReboursFini'];

    public $pourcentageInvesti = 0, $commentTauxList = [], $lastActivity, $commentCount, $nombreParticipants,
        $tauxTrade, $wallet, $coi, $user_connecte, $timeFin, $isNegociationActive;
    protected $TransactionService;




    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Récupérer l'ID de l'utilisateur qui a demnder le credit depuis les données de la notification
        $this->userId = $this->notification->data['user_id'];
        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($this->userId);
        $userNumber = $this->userDetails->phone;
        $this->TransactionService = new TransactionService();

        // Récupérer l'ID de l'utilisateur connecté
        $this->user_connecte = Auth::id();
        $this->wallet = Wallet::where('user_id', Auth::id())->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$this->wallet) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }



        $this->solde = $this->wallet ? $this->wallet->coi->Solde : 0;

        // Récupérer l'ID de la demande de credi du userId
        $demandeId = $this->notification->data['demande_id'];
        $this->demandeCredit = DemandeCredi::where('demande_id', $demandeId)->first();

        $this->isNegociationActive = !$this->demandeCredit->count;
        $this->nombreParticipants = 1;
        $this->commentCount = 1;

        //Récupérer l'objet COI associé
        $this->coi = $this->wallet->coi; // L'objet `coi`, supposant qu'il est une relation avec le wallet

        // Vérifier que l'objet COI existe
        if (!$this->coi) {
            session()->flash('error', 'Votre compte COI est introuvable.');
            return;
        }

        // Vérifier que le solde est suffisant
        if ($this->coi->Solde < $this->montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }
        // Date du plus ancien commentaire
        $this->timeFin = Carbon::parse($this->demandeCredit->date_fin);

        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $this->userInPromir = UserPromir::where('numero', $userNumber)->first();

        if ($this->userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $this->crediScore = CrediScore::where('id_user', $this->userInPromir->id)->first();
        }

        // Vérifier si un investisseur a payé la totalité du demandeCredit
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$this->demandeCredit->montant])
            ->value('id_invest'); // Récupérer l'ID de l'investisseur qui a payé tout, s'il existe

        // Log de l'investisseur qui a payé tout
        Log::info('Investisseur qui a payé tout pour le demandeCredit ID: ' . $this->demandeCredit->id . ', Investisseur ID: ' . $this->investisseurQuiAPayeTout);

        $this->listenTaux();
        $this->updatedMontant();
    }

    // Méthode déclenchée lorsque le compte à rebours est terminé
    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du demandeCredit
        $this->demandeCredit->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            )
        ]);

        //Vous pouvez également émettre un événement pour informer l'utilisateur
        $this->dispatch(
            'formSubmitted',
            'Temps écoule, Négociation terminé.'
        );
    }

    #[On('echo:comments,CommentSubmittedTaux')]
    public function listenTaux()
    {
        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('code_unique', $this->demandeCredit->demande_id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();
    }

    #[On('echo:ajout-montant,AjoutMontantF')]
    public function updatedMontant()
    {
        $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->sum('montant'); // Somme des montants investis
        // Calculer la somme restante à investir
        $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie; // Montant total - Somme investie
        // Calculer le pourcentage investi
        if ($this->demandeCredit->montant > 0) {
            $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
        }
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', operator: $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');
        // Récupérer la somme totale de tous les montants ajoutés pour ce demandeCredit par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du demandeCredit
        $this->montantVerifie = $totalAjoute >= $this->demandeCredit->montant;
    }

    #[On('echo:debut-negociation,DebutDeNegociation')]
    public function actualisation()
    {
        $this->dispatch('refreshPage'); // Émission d'un événement Livewire

    }

    public function confirmer()
    {
        if ($this->montant <= 0) {
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }


        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {
            // Sauvegarde du montant dans AjoutMontant
            $ajoumontant = $this->ajouterMontant();

            // Mise à jour du solde du COI
            $this->mettreAJourSoldeCoi($this->coi);
            // Vérification de l'investisseur qui a payé la totalité
            $this->verifierInvestisseurQuiAPayeTout();

            // Mise à jour du pourcentage et des autres propriétés
            $this->mettreAJourProprietes();

            // Si le montant total est atteint, déclenchement de l'événement
            $this->debutNegociationSiMontantTotalAtteint();

            if ($this->pourcentageInvesti == 100 && $this->investisseurQuiAPayeTout) {
                $this->TransactionService->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Gele', $this->montant, $this->generateIntegerReference(),  'Gelement pour negociation financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
            } else {
                $this->TransactionService->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $this->montant, $this->generateIntegerReference(),  'financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
            }

            broadcast(new AjoutMontantF($ajoumontant))->toOthers();

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
        // Réinitialiser le montant saisi et le drapeau de vérification de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;
    }
    public function approuver($montant)
    {
        $service = new CreditApprovalService();

        $result = $service->approuver(
            $montant,
            $this->userId,
            $this->demandeCredit,
            $this->coi,
            $this->notification,
            fn() => $this->generateIntegerReference(),   // callable
            fn(...$args) => $this->TransactionService->createTransaction(...$args) // callable
        );

        if (isset($result['error'])) {
            session()->flash('error', $result['error']);
        } else {
            session()->flash('success', $result['success']);
            $this->montant = '';
            $this->insuffisant = false;
        }
    }

    public function commentForm(TauxSubmissionService $tauxService)
    {
        $this->validate([
            'tauxTrade' => 'required|numeric|min:0',
        ]);

        $success = $tauxService->handleCommentForm(
            $this->demandeCredit,
            $this->user_connecte,
            $this->wallet,
            $this->coi,
            $this->tauxTrade,
            $this->userId
        );

        if ($success) {
            $this->montant = '';
            $this->tauxTrade = '';
            $this->insuffisant = false;

            $this->commentTauxList = CommentTaux::with('investisseur')
                ->where('code_unique', $this->demandeCredit->demande_id)
                ->orderBy('taux', 'asc')
                ->get();

            $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
                ->distinct()
                ->count('id_invest');
        }
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }


    private function ajouterMontant()
    {

        return AjoutMontant::create([
            'montant' => $this->montant,
            'id_invest' => Auth::id(),
            'id_emp' => $this->demandeCredit->id_user,
            'id_demnd_credit' => $this->demandeCredit->id,
        ]);
    }

    private function mettreAJourSoldeCoi($coi)
    {
        $coi->Solde -= $this->montant;
        $coi->save();
    }

    private function verifierInvestisseurQuiAPayeTout()
    {
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$this->demandeCredit->montant])
            ->value('id_invest');
    }

    private function mettreAJourProprietes()
    {
        $this->sommeInvestie = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');
        $this->sommeRestante = $this->demandeCredit->montant - $this->sommeInvestie;
        $this->pourcentageInvesti = ($this->sommeInvestie / $this->demandeCredit->montant) * 100;

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');
        // Récupérer la somme totale de tous les montants ajoutés pour ce demandeCredit par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du demandeCredit
        $this->montantVerifie = $totalAjoute >= $this->demandeCredit->montant;
    }

    private function debutNegociationSiMontantTotalAtteint()
    {
        // passage a la negociation de taux lorsque cest une seule personne qui tt
        if ($this->pourcentageInvesti == 100 && $this->investisseurQuiAPayeTout) {

            $this->demandeCredit->update([
                'status' => 'negociation',
            ]);
            // gelement le montant dans la table `gelement`
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $this->montant,
                'reference_id' => $this->demandeCredit->demande_id,
            ]);



            // Si un investisseur a payé le montant total, déclencher l'événement
            if ($this->investisseurQuiAPayeTout) {
                // Déclencher l'événement `DebutDeNegociation`
                broadcast(new DebutDeNegociation($this->demandeCredit, $this->investisseurQuiAPayeTout));
                $this->dispatch('DebutDeNegociation', $this->demandeCredit, $this->investisseurQuiAPayeTout);
            }
        }
        // ici cest la partir du grouper normal
        if ($this->pourcentageInvesti == 100 && !$this->investisseurQuiAPayeTout) {

            $this->demandeCredit->update([
                'count' => true,
            ]);
        }
    }

    public function refuser()
    {
        $this->notification->update(['reponse' => 'refuser']);
        $this->demandeCredit->update(['status' => 'refuser']);

        session()->flash('error', 'Demande de credit refuser avec succes.L\'utilisateur seras informe de votre reponse ');

        $owner = User::find($this->userId);

        $reason = "L'achat dépasse la limite de crédit autorisée.";
        Notification::send($owner, new RefusAchat($reason));
    }
    public function joursRestants()
    {
        $dateFin = Carbon::parse($this->demandeCredit->date_fin);
        $dateActuelle = Carbon::parse($this->demandeCredit->date_debut);
        $joursRestants = $dateActuelle->diffInDays($dateFin);
        return max(0, $joursRestants); // Retournez 0 si le demandeCredit est déjà terminé
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

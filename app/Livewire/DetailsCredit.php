<?php

namespace App\Livewire;

use App\Events\CommentSubmittedTaux;
use App\Events\DebutDeNegociation;
use App\Models\AjoutMontant;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\CommentTaux;
use App\Models\Countdown;
use App\Models\CrediScore;
use App\Models\credits;
use App\Models\DemandeCredi;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserPromir;
use App\Models\Wallet;
use App\Notifications\RefusAchat;
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
    public $investisseurQuiAPayeTout;
    public $montantVerifie = false;
    public $sommeRestante = 0;
    public $montant = ''; // Stocke le montant saisi
    protected $listeners = ['compteReboursFini'];

    public $pourcentageInvesti = 0;
    public $commentTauxList = [];
    public $tauxTrade;




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

        // Récupérer la somme totale de tous les montants ajoutés pour ce demandeCredit par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du demandeCredit
        $this->montantVerifie = $totalAjoute >= $this->demandeCredit->montant;

        // Vérifier si un investisseur a payé la totalité du demandeCredit
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$this->demandeCredit->montant])
            ->value('id_invest'); // Récupérer l'ID de l'investisseur qui a payé tout, s'il existe

        // Log de l'investisseur qui a payé tout
        Log::info('Investisseur qui a payé tout pour le demandeCredit ID: ' . $this->demandeCredit->id . ', Investisseur ID: ' . $this->investisseurQuiAPayeTout);

        $this->listenTaux();
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

        // Optionnel : Vous pouvez également émettre un événement pour informer l'utilisateur
        $this->dispatch(
            'formSubmitted',
            'Temps écoule, Négociation terminé.'
        );
        // $close = true; // Votre logique d'éligibilité ici

        // if ($close) {
        //     $this->dispatch('submittion', $close,);
        // }

    }

    #[On('echo:comments,CommentSubmittedTaux')]
    public function listenTaux()
    {
        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('id_demande_credit', $this->demandeCredit->id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();
    }
    public function updatedMontant()
    {
        // Vérifier si le montant saisi dépasse le solde
        $this->insuffisant = !empty($this->montant) && $this->montant > $this->solde;
    }

    #[On('echo:debut-negociation,DebutDeNegociation')]
    public function actualisation()
    {
        $this->dispatch('refreshPage'); // Émission d'un événement Livewire

    }

    public function confirmer()
    {

        // Vérifier que le montant est valide, non vide, numérique et supérieur à zéro
        $montant = !empty($this->montant) ? floatval($this->montant) : floatval($this->demandeCredit->montant);
        // dd($montant);
        if ($montant <= 0) {
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }
        $user = auth()->user();


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
            // Vérifier si c'est la première soumission
            $ajoutMontant = AjoutMontant::where('id_demnd_credit', $user->id)
                ->where('id_demnd_credit', $this->demandeCredit->id)
                ->first();


            // Sauvegarder le montant dans la table `ajout_montant`
            $ajoumontant = AjoutMontant::create([
                'montant' => isset($ajoutMontant) ? 0 : $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->demandeCredit->id_user, // Assurez-vous que cet ID existe
                'id_demnd_credit' => $this->demandeCredit->id,
            ]);

            // Log après l'ajout du montant
            Log::info('Montant ajouté avec succès pour l\'utilisateur ID: ' . Auth::id() . ', ID de l\'ajout montant: ' . $ajoumontant->id);


            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte
            if ($coi) {
                $coi->Solde -= $montant; // Débiter le montant du solde du COI
                $coi->save();
            }

            // // Mettre à jour ou créer un enregistrement dans la table CFA
            // $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // // Mettre à jour ou créer un enregistrement dans la table CFA

            // if ($cfa) {
            //     // Si le compte CFA existe, on additionne le montant
            //     $cfa->Solde += $montant; // Ajoute le montant au solde existant dans le CFA
            //     $cfa->save();
            // }

            // if (!$ajoutMontant) {
            //     $reference_id = $this->generateIntegerReference();

            //     $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $reference_id,  'financement  de credit d\'achat',  'effectué', $coi->type_compte);
            //     $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Réception', $montant, $reference_id,  'reception de financement  de credit d\'achat',  'effectué', $cfa->type_compte);
            // }

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
        // Récupérer la somme totale de tous les montants ajoutés pour ce demandeCredit par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du demandeCredit
        $this->montantVerifie = $totalAjoute >= $this->demandeCredit->montant;

        // Vérifier si un investisseur a payé la totalité du demandeCredit
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$this->demandeCredit->montant])
            ->value('id_invest'); // Récupérer l'ID de l'investisseur qui a payé tout, s'il existe
        // Si un investisseur a payé le montant total, déclencher l'événement
        if ($this->investisseurQuiAPayeTout) {
            // Déclencher l'événement `DebutDeNegociation`
            broadcast(new DebutDeNegociation($this->demandeCredit, $this->investisseurQuiAPayeTout));
            $this->dispatch('DebutDeNegociation', $this->demandeCredit, $this->investisseurQuiAPayeTout);
        }
        // Log de l'investisseur qui a payé tout
        Log::info('Investisseur qui a payé tout pour le demandeCredit ID: ' . $this->demandeCredit->id . ', Investisseur ID: ' . $this->investisseurQuiAPayeTout);
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

            //  Calculer la portion journalière en fonction du montant et de la durée.
            // Assurez-vous que les dates sont bien des instances de Carbon
            $dateDebut = Carbon::parse($this->demandeCredit->date_debut);
            $dateFin = Carbon::parse($this->demandeCredit->duree);
            $jours = $dateDebut->diffInDays($dateFin);
            $portion_journaliere = $jours > 0 ? $montant / $jours : 0;

            $montantTotal =  $montant / (1 + $demandeCredit->taux / 100);
            // Mettre à jour ou créer un enregistrement dans la table credits
            credits::create([
                'emprunteur_id' => $this->userId,
                'investisseurs' => [Auth::id()],
                'montant' => $montantTotal,
                'taux_interet' => $demandeCredit->taux,
                'date_debut' => $demandeCredit->date_debut,
                'date_fin' => $demandeCredit->duree,
                'portion_journaliere' => $portion_journaliere,
                'statut' => 'en_cours',
            ]);

            $reference_id = $this->generateIntegerReference();

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $reference_id,  'financement  de Crédit d\'achat',  'effectué', $coi->type_compte);
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Réception', $montant, $reference_id,  'reception de Fonds  de Credit d\'achat',  'effectué', $cfa->type_compte);


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

    public function commentForm()
    {
        // Validation du champ tauxTrade
        $this->validate([
            'tauxTrade' => 'required|numeric|min:0',
        ]);

        // Vérification de l'objet demandeCredit
        if (!$this->demandeCredit || !$this->demandeCredit->id) {
            session()->flash('error', 'Le demandeCredit est introuvable.');
            return;
        }

        $user = auth()->user();

        $userWallet = Wallet::where('user_id', $user->id)->first();
        $coiWallet = Coi::where('id_wallet', $userWallet->id)->first();
        if (!$coiWallet  || $coiWallet->Solde < $this->demandeCredit->montant) {
            session()->flash('error', 'Votre solde est insuffisant pour soumettre une offre. Montant requis : ' . $this->demandeCredit->montant . ' CFA.');
            return;
        }

        // Vérifier si c'est la première soumission pour chaque utilisateur connecté
        $ajoutMontant = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->where('id_invest', $user->id) // Corrected to check against user_id
            ->first();

        if (!$ajoutMontant) {
            // Appeler la fonction confirmer si c'est la première soumission
            $this->confirmer();
        }

        // Appeler la fonction pour afficher le formulaire de commentaire
        $this->ElementcommentForm();
    }

    protected function ElementcommentForm()
    {

        // Insérer dans la table commentTaux
        try {
            $commentTaux = CommentTaux::create([
                'taux' => $this->tauxTrade,
                'id_invest' => auth()->id(),
                'id_emp' => $this->demandeCredit->id_user,
                'id_demande_credit' => $this->demandeCredit->id,
            ]);

            // Réinitialiser le champ tauxTrade après l'insertion
            $this->tauxTrade = '';
            broadcast(new CommentSubmittedTaux($this->tauxTrade,  $commentTaux->id))->toOthers();


            // Optionnel: Ajouter une notification ou un message de succès
            session()->flash('message', 'Commentaire sur le taux ajouté avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }

        // Commenter cette ligne une fois que vous avez vérifié

        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('id_demande_credit', $this->demandeCredit->id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();

        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique',  $this->demandeCredit->id)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $this->userId,
                // 'start_time' => $this->dateFin,
                'start_time' => now(),
                'difference' => 'projet_taux',
                'code_unique' =>  $this->demandeCredit->id,
            ]);
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




    public function refuser()
    {
        $this->notification->update(['reponse' => 'refuser']);
        session()->flash('error', 'Demande de credit refuser avec succes.L\'utilisateur seras informe de votre reponse ');

        $owner = User::find($this->user_id);

        $reason = "L'achat dépasse la limite de crédit autorisée.";
        Notification::send($owner, new RefusAchat($reason));
    }
    public function joursRestants()
    {
        $dateFin = \Carbon\Carbon::parse($this->demandeCredit->date_fin);
        $dateActuelle = now();
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

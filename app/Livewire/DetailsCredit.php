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
use App\Models\credits;
use App\Models\credits_groupé;
use App\Models\DemandeCredi;
use App\Models\gelement;
use App\Models\remboursements;
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
    public $wallet;
    public $coi;
    public $user_connecte;
    public $timeFin;




    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        // Récupérer l'ID de l'utilisateur qui a demnder le credit depuis les données de la notification
        $this->userId = $this->notification->data['user_id'];
        // Optionnel : si tu veux faire d'autres actions avec l'utilisateur
        $this->userDetails = User::find($this->userId);
        $userNumber = $this->userDetails->phone;

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
                $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Gele', $this->montant, $this->generateIntegerReference(),  'Gelement pour negociation financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
            } else {
                $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $this->montant, $this->generateIntegerReference(),  'financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
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
        // Convertir le montant en float
        $montant = floatval($montant);

        // Vérification si le montant est valide
        if ($montant <= 0) {
            session()->flash('error', 'Montant invalide.');
            return;
        }


        // Récupérer le wallet de l'utilisateur demandeur
        $walletDemandeur = Wallet::where('user_id', $this->userId)->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$walletDemandeur) {
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }

        // Vérifier que le solde du wallet est suffisant
        if ($this->coi->Solde < $montant) {
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {

            if ($this->coi) {
                // Vérifie si le solde est suffisant pour le débit
                if ($this->coi->Solde >= $montant) {
                    $this->coi->Solde -= $montant; // Débiter le montant du solde du COI
                    $this->coi->save();
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

            $montantTotal = $montant * (1 + $this->demandeCredit->taux / 100);
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

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Envoie', $montant, $this->generateIntegerReference(),  'Financement  de Crédit d\'achat',  'effectué', $this->coi->type_compte);
            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Réception', $montant, $this->generateIntegerReference(),  'Réception de Fonds  de Credit d\'achat',  'effectué', $cfa->type_compte);


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

        // Vérifier si c'est la première soumission pour chaque utilisateur connecté
        $ajoutMontant = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->where('id_invest', $this->user_connecte)
            ->first();

        if (!$ajoutMontant) {
            // Vérifier si le wallet existe et si le solde est insuffisant par rapport au montant requis
            if ($this->coi && $this->coi->Solde < $this->demandeCredit->montant) {
                // Si le solde est insuffisant, afficher un message d'erreur et arrêter l'exécution
                session()->flash('error', 'Votre solde est insuffisant pour soumettre une offre. Montant requis : ' . $this->demandeCredit->montant . ' CFA.');
                return;  // Arrêter l'exécution de la fonction
            }

            // Si le solde est suffisant, appeler la fonction confirmer2
            $this->confirmer2();
        }


        // Appeler la fonction pour afficher le formulaire de commentaire
        $this->ElementcommentForm();
    }
    public function confirmer2()
    {
        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();

        try {
            // Sauvegarde du montant dans AjoutMontant
            AjoutMontant::create([
                'montant' => $this->demandeCredit->montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->demandeCredit->id_user,
                'id_demnd_credit' => $this->demandeCredit->id,
            ]);

            // gelement le montant dans la table `gelement`
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $this->demandeCredit->montant,
                'reference_id' => $this->demandeCredit->demande_id,
            ]);


            // Mettre à jour le solde du COI (Compte des Opérations d'Investissement)
            $coi = $this->wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte
            if ($coi) {
                $coi->Solde -= $this->demandeCredit->montant; // Débiter le montant du solde du COI
                $coi->save();
            }

            $this->createTransaction(Auth::id(), $this->demandeCredit->id_user, 'Gele', $this->demandeCredit->montant, $this->generateIntegerReference(),  'financement  de credit d\'achat',  'effectué', $this->coi->type_compte);

            DB::commit();
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

        // Réinitialiser le montant saisi et le drapeau de vérification de solde insuffisant
        $this->montant = '';
        $this->insuffisant = false;


        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_demnd_credit', $this->demandeCredit->id)
            ->distinct()
            ->count('id_invest');
    }
    protected function ElementcommentForm()
    {
        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();
        // Insérer dans la table commentTaux
        try {

            $commentTaux = CommentTaux::create([
                'taux' => $this->tauxTrade,
                'code_unique' => $this->demandeCredit->demande_id,
                'id_invest' => auth()->id(),
                'id_emp' => $this->demandeCredit->id_user,
            ]);

            // Réinitialiser le champ tauxTrade après l'insertion
            $this->tauxTrade = '';
            broadcast(new CommentSubmittedTaux($this->tauxTrade,  $commentTaux->id))->toOthers();

            // Committer la transaction
            DB::commit();
            // Optionnel: Ajouter une notification ou un message de succès
            session()->flash('message', 'Commentaire sur le taux ajouté avec succès.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }

        // Commenter cette ligne une fois que vous avez vérifié

        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('code_unique', $this->demandeCredit->demande_id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();

        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique',  $this->demandeCredit->demande_id)
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
                'difference' => 'credit_taux',
                'code_unique' =>  $this->demandeCredit->demande_id,
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

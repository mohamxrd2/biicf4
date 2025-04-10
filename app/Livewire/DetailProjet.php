<?php

namespace App\Livewire;

use App\Events\AjoutMontantF;
use App\Events\CommentSubmittedTaux;
use App\Events\DebutDeNegociation;
use App\Events\OldestCommentUpdated;
use App\Models\AjoutAction;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\gelement;
use App\Models\Projet;
use App\Models\Wallet;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\CommentTaux;
use App\Models\Transaction;
use App\Models\AjoutMontant;
use App\Models\Countdown;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DetailProjet extends Component
{
    public $projet;
    public $images = [];
    public $montant = ''; // Stocke le montant saisi
    public $action; // Déclaration de la propriété action pour le binding
    public $solde; // Stocke le solde de l'utilisateur
    public $insuffisant = false; // Pour vérifier si le solde est insuffisant
    public $nombreInvestisseursDistinct = 0;
    public $nombreInvestisseursDistinctAction = 0;
    public $sommeRestante = 0;
    public $sommeRestanteAction = 0;

    public $pourcentageInvesti = 0;
    public $pourcentageInvestiAction = 0;

    public $sommeInvestie = 0;
    public $sommeInvestieActions = 0;
    public $investisseurQuiAPayeTout;
    public $montantVerifie = false;
    public $montantVerifieAction = false;

    public $tauxTrade;
    public $commentTauxList = [];
    public $dateFin;
    public $timer;
    public $wallet;
    public $coi;

    protected $listeners = ['compteReboursFini'];
    public function mount($id)
    {
        $this->projet = Projet::with('demandeur')->find($id);
        $this->timer = $this->projet->durer;
        $this->images = array_filter([
            $this->projet->photo1,
            $this->projet->photo2,
            $this->projet->photo3,
            $this->projet->photo4,
            $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
        ]);

        $userId = Auth::id();
        // $this->action = 0; // Valeur par défaut

        $this->wallet = Wallet::where('user_id', $userId)->first();
        $this->coi = $this->wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte

        $this->solde = $this->coi ? $this->coi->Solde : 0;
        // Vérifier que l'utilisateur possède un wallet
        if (!$this->wallet) {
            Log::error('Wallet introuvable pour l\'utilisateur ID: ' . Auth::id());
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }



        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinctAction = AjoutAction::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)
            ->sum('montant'); // Somme des montants investis

        //nbre d'action investit
        $this->sommeInvestieActions = AjoutAction::where('id_projet', $this->projet->id)
            ->sum('nombreActions');

        // Calculer le pourcentage investi en utilisant Portion_obligt si elle existe, sinon this->projet->montant
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ? $this->projet->Portion_obligt : $this->projet->montant;

        if ($montant > 0) {
            $this->pourcentageInvesti = ($this->sommeInvestie / $montant) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
        }



        // Calculer le pourcentage d'actions investies
        if ($this->projet->nombreActions > 0) {
            $this->pourcentageInvestiAction = ($this->sommeInvestieActions / $this->projet->nombreActions) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvestiAction = 0; // Si le montant est 0, le pourcentage est 0
        }

        // Calculer la somme restante à investir
        $this->sommeRestante = $montant - $this->sommeInvestie; // Montant total - Somme investie

        $this->sommeRestanteAction = $this->projet->nombreActions - $this->sommeInvestieActions;



        // Récupérer la somme totale de tous les montants ajoutés pour ce this->projet par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_projet', $this->projet->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du this->projet
        $this->montantVerifie = $totalAjoute >= $montant;

        // Vérifier si un investisseur a payé la totalité du this->projet
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_projet', $this->projet->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$montant])
            ->value('id_invest'); // Récupérer l'ID de l'investisseur qui a payé tout, s'il existe

        // Log de l'investisseur qui a payé tout
        Log::info('Investisseur qui a payé tout pour le this->projet ID: ' . $this->projet->id . ', Investisseur ID: ' . $this->investisseurQuiAPayeTout);



        // Vérifier si le nombre d'action du this->projet est atteint
        $this->montantVerifieAction = AjoutAction::where('id_projet', $this->projet->id)
            ->where('nombreActions', $this->projet->nombreActions)
            ->exists();

        $this->dateFin = \Carbon\Carbon::parse($this->projet->durer);

        $this->listenTaux();
    }

    // Méthode déclenchée lorsque le compte à rebours est terminé
    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du this->projet
        $this->projet->update([
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
    }

    #[On('echo:comments,CommentSubmittedTaux')]
    public function listenTaux()
    {
        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('projet_id', $this->projet->id)
            ->orderBy('taux', 'asc')
            ->orderBy('created_at', 'asc')
            ->get();
    }

    #[On('echo:ajout-montant,AjoutMontantF')]
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
        $montant = floatval($this->montant);

        if (empty($this->montant) || !is_numeric($montant) || $montant <= 0) {
            Log::warning('Montant invalide saisi par l\'utilisateur ID: ' . Auth::id() . ', Montant: ' . $this->montant);
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }



        // Début de la transaction
        DB::beginTransaction();

        try {

            // Sauvegarder le montant dans la table `ajout_montant`
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->projet->demandeur->id,
                'id_projet' => $this->projet->id,
            ]);


            // Mettre à jour le solde du COI
            $coi = $this->wallet->coi;
            if ($coi) {
                $coi->Solde -= $montant;
                $coi->save();
                Log::info('Solde COI mis à jour pour l\'utilisateur ID: ' . Auth::id() . ', Nouveau solde COI: ' . $coi->Solde);
            }

            // Calculer le pourcentage investi en utilisant Portion_obligt si elle existe, sinon this->projet->montant
            $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ?
                $this->projet->Portion_obligt : $this->projet->montant;
            $this->verifierInvestisseurQuiAPayeTout();
            $this->mettreAJourProprietes();
            $this->debutNegociationSiMontantTotalAtteint();

            if ($this->pourcentageInvesti == 100 && $this->investisseurQuiAPayeTout) {
                $this->createTransaction(Auth::id(), $this->projet->id_user, 'Gele', $this->montant, $this->generateIntegerReference(),  'Gelement pour negociation financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
            } else {
                $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $this->montant, $this->generateIntegerReference(),  'financement  de credit d\'achat',  'effectué', $this->coi->type_compte);
            }

            broadcast(new AjoutMontantF($ajoumontant))->toOthers();



            // Committer la transaction
            DB::commit();
        } catch (\Exception $e) {
            // Rollback en cas d'erreur
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout du montant pour l\'utilisateur ID: ' . Auth::id() . ', Erreur: ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

        // Message de succès
        session()->flash('success', 'Le montant a été ajouté avec succès.');

        // Réinitialiser le montant saisi et les indicateurs
        $this->montant = '';
        $this->insuffisant = false;
    }

    private function debutNegociationSiMontantTotalAtteint()
    {
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ?
            $this->projet->Portion_obligt : $this->projet->montant;

        // passage a la negociation de taux lorsque cest une seule personne qui tt
        if ($this->investisseurQuiAPayeTout) {

            $this->projet->update([
                'status' => 'negociation',
            ]);

            // gelement le montant dans la table `gelement`
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $montant,
                'reference_id' => $this->projet->id,
            ]);

            // Déclencher l'événement `DebutDeNegociation`
            broadcast(new DebutDeNegociation($this->projet, $this->investisseurQuiAPayeTout));
            $this->dispatch('DebutDeNegociation', $this->projet, $this->investisseurQuiAPayeTout);
        }
        // Vérifier si le pourcentage investi est de 100% ou plus
        if ($this->pourcentageInvesti == 100 && !$this->investisseurQuiAPayeTout) {
            $this->projet->update([
                'count' => true
            ]);
        }
    }
    private function verifierInvestisseurQuiAPayeTout()
    {
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ? $this->projet->Portion_obligt : $this->projet->montant;
        // Vérifier si un investisseur a payé la totalité du this->projet
        $this->investisseurQuiAPayeTout = AjoutMontant::where('id_projet', $this->projet->id)
            ->select('id_invest')
            ->groupBy('id_invest')
            ->havingRaw('SUM(montant) >= ?', [$montant])
            ->value('id_invest'); // Récupérer l'ID de l'investisseur qui a payé tout, s'il existe
    }
    private function mettreAJourProprietes()
    {
        // Calculer le pourcentage investi en utilisant Portion_obligt si elle existe, sinon this->projet->montant
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ? $this->projet->Portion_obligt : $this->projet->montant;
        // Rafraîchir les propriétés du composant
        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)->sum('montant');
        $this->sommeRestante = $montant - $this->sommeInvestie;
        $this->pourcentageInvesti = ($this->sommeInvestie / $montant) * 100;


        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        // Récupérer la somme totale de tous les montants ajoutés pour ce this->projet par tous les utilisateurs
        $totalAjoute = AjoutMontant::where('id_projet', $this->projet->id)->sum('montant');

        // Vérifier si la somme totale atteint ou dépasse le montant du this->projet
        $this->montantVerifie = $totalAjoute >= $montant;
    }

    public function confirmerAction()
    {
        // Log du début de la méthode
        Log::info('Démarrage de la méthode confirmer pour l\'utilisateur ID: ' . Auth::id());

        // Récupérer le nombre d'actions saisies et convertir en float
        $actions = floatval($this->action);

        // Récupérer le prix unitaire de l'action (Portion_action)
        $prixUnitaire = floatval($this->projet->Portion_action);

        // Calculer le montant total (nombre d'actions * prix unitaire)
        $montant = $actions * $prixUnitaire;
        // Vérifier que le nombre d'actions est supérieur à zéro
        if ($actions <= 0) {
            Log::error('Le nombre d\'actions doit être supérieur à zéro. Valeur reçue : ' . $actions);
            return;
        }

        if (empty($montant) || !is_numeric($montant) || $montant <= 0) {
            Log::warning('Montant invalide saisi par l\'utilisateur ID: ' . Auth::id() . ', Montant: ' . $montant);
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }
        // Récupérer le this->projet
        $projet = $this->projet;

        // Vérifiez si le this->projet et le demandeur existent
        if (!$this->projet || !$this->projet->demandeur || !$this->projet->demandeur->id) {
            Log::error('Projet ou demandeur introuvable pour l\'utilisateur ID: ' . Auth::id());
            session()->flash('error', 'Le this->projet ou le demandeur est introuvable.');
            return;
        }

        // Log du this->projet et demandeur
        Log::info('Projet trouvé, ID du this->projet: ' . $this->projet->id . ', ID du demandeur: ' . $this->projet->demandeur->id);

        // Récupérer le wallet de l'investisseur
        $wallet = Wallet::where('user_id', Auth::id())->first();

        // Récupérer le wallet du demandeur
        $walletDemandeur = Wallet::where('user_id', $this->projet->id_user)->first();

        // Vérifier que l'utilisateur possède un wallet
        if (!$wallet) {
            Log::error('Wallet introuvable pour l\'utilisateur ID: ' . Auth::id());
            session()->flash('error', 'Votre portefeuille est introuvable.');
            return;
        }

        // Vérifier que le solde du wallet est suffisant
        if ($wallet->coi->Solde < $montant) {
            Log::warning('Solde insuffisant pour l\'utilisateur ID: ' . Auth::id() . ', Solde: ' . $wallet->coi->Solde . ', Montant requis: ' . $montant);
            session()->flash('error', 'Votre solde est insuffisant pour cette transaction.');
            return;
        }

        // Début de la transaction
        DB::beginTransaction();

        try {
            // Log avant l'ajout du montant
            Log::info('Ajout du montant pour l\'utilisateur ID: ' . Auth::id() . ', Montant: ' . $montant);

            // Sauvegarder le montant dans la table `ajout_montant`
            $ajoumontant = AjoutAction::create([
                'nombreActions' => $actions,
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->projet->demandeur->id,
                'id_projet' => $this->projet->id,
            ]);

            // Log après l'ajout du montant
            Log::info('Montant ajouté avec succès pour l\'utilisateur ID: ' . Auth::id() . ', ID de l\'ajout montant: ' . $ajoumontant->id);

            // Mettre à jour le solde du COI
            $coi = $wallet->coi;
            if ($coi) {
                $coi->Solde -= $montant;
                $coi->save();
                Log::info('Solde COI mis à jour pour l\'utilisateur ID: ' . Auth::id() . ', Nouveau solde COI: ' . $coi->Solde);
            }

            // // Mettre à jour ou créer un enregistrement dans la table CFA du demandeur
            // $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            // if ($cfa) {
            //     $cfa->Solde += $montant;
            //     $cfa->save();
            //     Log::info('Solde CFA mis à jour pour le demandeur ID: ' . $this->projet->id_user . ', Nouveau solde CFA: ' . $cfa->Solde);
            // }

            // Générer une référence de transaction
            $reference_id = $this->generateIntegerReference();
            Log::info('Référence de transaction générée: ' . $reference_id);

            // Créer deux transactions
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $montant, $reference_id, 'Achat d\'action', 'effectué', $coi->type_compte);
            // $this->createTransaction(Auth::id(), $this->projet->id_user, 'Réception', $montant, $reference_id, 'Réception de fond', 'effectué', $cfa->type_compte);
            Log::info('Transactions créées avec succès pour l\'utilisateur ID: ' . Auth::id());

            // Committer la transaction
            DB::commit();
            Log::info('Transaction committée avec succès pour l\'utilisateur ID: ' . Auth::id());
        } catch (\Exception $e) {
            // Rollback en cas d'erreur
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout du montant pour l\'utilisateur ID: ' . Auth::id() . ', Erreur: ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }

        $this->dispatch(
            'formSubmitted',
            'Achat effectué avec succ.'
        );

        // Réinitialiser le montant saisi et les indicateurs
        $this->montant = '';
        $this->insuffisant = false;

        // Rafraîchir les propriétés du composant
        //nbre d'aqction investit
        $this->sommeInvestieActions = AjoutAction::where('id_projet', $this->projet->id)
            ->sum('nombreActions');
        $this->sommeRestanteAction = $this->projet->nombreActions - $this->sommeInvestieActions;
        $this->pourcentageInvestiAction  = ($this->sommeInvestieActions / $this->projet->nombreActions) * 100;

        // Log de mise à jour des sommes investies
        Log::info('Somme investie mise à jour pour le this->projet ID: ' . $this->projet->id . ', Somme investie: ' . $this->sommeInvestie);

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinctAction = AjoutAction::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        // Vérifier si le nombre d'action du this->projet est atteint
        $this->montantVerifieAction = AjoutAction::where('id_projet', $this->projet->id)
            ->where('nombreActions', $this->projet->nombreActions)
            ->exists();

        // Log final
        Log::info('Montant vérifié pour le this->projet ID: ' . $this->projet->id . ', Montant atteint: ' . ($this->montantVerifie ? 'Oui' : 'Non'));
    }


    public function commentForm()
    {
        // Validation du champ tauxTrade
        $this->validate([
            'tauxTrade' => 'required|numeric|min:0',
        ]);



        $user = auth()->user();

        // Calculer le pourcentage investi en utilisant Portion_obligt si elle existe, sinon this->projet->montant
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ? $this->projet->Portion_obligt : $this->projet->montant;

        // Vérifier si c'est la première soumission pour chaque utilisateur connecté
        $ajoutMontant = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('id_invest', $user->id) // Corrected to check against user_id
            ->first();

        if (!$ajoutMontant) {
            // Vérifier si le wallet existe et si le solde est insuffisant par rapport au montant requis
            if ($this->coi && $this->coi->Solde < $montant) {
                // Si le solde est insuffisant, afficher un message d'erreur et arrêter l'exécution
                session()->flash('error', 'Votre solde est insuffisant pour soumettre une offre. Montant requis : ' . $montant  . ' CFA.');
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
        // Vérifier que le montant est valide, non vide, numérique et supérieur à zéro
        $montant = isset($this->projet->Portion_obligt) && $this->projet->Portion_obligt > 0 ? $this->projet->Portion_obligt : $this->projet->montant;

        if ($montant <= 0) {
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }

        DB::beginTransaction();

        try {

            // Sauvegarder le montant dans la table `ajout_montant`
            AjoutMontant::create([
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $this->projet->id_user,
                'id_projet' => $this->projet->id,
            ]);

            // gelement le montant dans la table `gelement`
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $montant,
                'reference_id' => $this->projet->id,
            ]);


            // Mettre à jour le solde du COI
            $coi = $this->wallet->coi;
            if ($coi) {
                $coi->Solde -= $montant;
                $coi->save();
            }

            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Gele', $montant, $this->generateIntegerReference(), 'financement de crédit d\'un this->projet', 'effectué', $coi->type_compte);

            DB::commit();
        } catch (\Exception $e) {
            // Rollback en cas d'erreur
            DB::rollBack();
            Log::error('Erreur lors de l\'ajout du montant pour l\'utilisateur ID: ' . Auth::id() . ', Erreur: ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l\'ajout du montant : ' . $e->getMessage());
            return;
        }


        // Réinitialiser le montant saisi et les indicateurs
        $this->montant = '';
        $this->insuffisant = false;


        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');
    }

    protected function ElementcommentForm()
    {

        // Utilisation d'une transaction pour garantir la cohérence des données
        DB::beginTransaction();
        try {
            $commentTaux = CommentTaux::create([
                'taux' => $this->tauxTrade,
                'id_invest' => auth()->id(),
                'id_emp' => $this->projet->id_user,
                'projet_id' => $this->projet->id,
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
            ->where('projet_id', $this->projet->id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();

        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique',  $this->projet->id)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $this->projet->demandeur->id,
                // 'start_time' => $this->dateFin,
                'start_time' => now(),
                'difference' => 'projet_taux',
                'code_unique' =>  $this->projet->id,
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

    public function joursRestants()
    {
        $dateActuelle = now();
        // $joursRestants = $dateActuelle->diffInDays($this->dateFin);
        $joursRestants = $dateActuelle->diffInDays($this->projet->created_at);
        return max(0, $joursRestants); // Retournez 0 si le this->projet est déjà terminé
    }

    public function render()
    {
        $aDejaContribue = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('id_invest', Auth::id())
            ->exists();

        return view('livewire.detail-projet', [
            'aDejaContribue' => $aDejaContribue,
            'joursRestants' => $this->joursRestants(),
            'images' => $this->images,
            'nombreInvestisseurs' => $this->nombreInvestisseursDistinct,
            'sommeRestante' => $this->sommeRestante,
            'pourcentageInvesti' => $this->pourcentageInvesti,
        ]);
    }
}

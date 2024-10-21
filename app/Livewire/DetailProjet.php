<?php

namespace App\Livewire;

use App\Events\CommentSubmittedTaux;
use App\Events\OldestCommentUpdated;
use App\Models\AjoutAction;
use App\Models\Cfa;
use App\Models\Coi;
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
    public $solde; // Stocke le solde de l'utilisateur
    public $insuffisant = false; // Pour vérifier si le solde est insuffisant
    public $nombreInvestisseursDistinct = 0;
    public $sommeRestante = 0;

    public $pourcentageInvesti = 0;

    public $sommeInvestie = 0;
    public $sommeInvestieActions = 0;
    public $montantVerifie = false;

    public $tauxTrade;
    public $commentTauxList = [];
    public $dateFin;

    protected $listeners = ['compteReboursFini'];
    public function mount($id)
    {
        $this->projet = Projet::with('demandeur')->find($id);
        $this->images = array_filter([
            $this->projet->photo1,
            $this->projet->photo2,
            $this->projet->photo3,
            $this->projet->photo4,
            $this->projet->photo5 // Ajoutez autant de photos que vous avez dans la base de données
        ]);

        $userId = Auth::id();

        $wallet = Wallet::where('user_id', $userId)->first();
        $coi = $wallet->coi;  // Assurez-vous que la relation entre Wallet et COI est correcte

        $this->solde = $coi ? $coi->Solde : 0;

        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)
            ->sum('montant'); // Somme des montants investis

        //somme investit pour les actions
        $this->sommeInvestieActions = AjoutAction::where('id_projet', $this->projet->id)
            ->sum('montant');

        // Calculer le pourcentage investi
        if ($this->projet->montant > 0) {
            $this->pourcentageInvesti = ($this->sommeInvestie / $this->projet->montant) * 100; // Calculer le pourcentage investi
        } else {
            $this->pourcentageInvesti = 0; // Si le montant est 0, le pourcentage est 0
        }

        // Calculer la somme restante à investir
        $this->sommeRestante = $this->projet->montant - $this->sommeInvestie; // Montant total - Somme investie

        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant) // Assurez-vous que le champ 'montant' existe dans votre modèle
            ->exists(); // Renvoie true si le montant existe

        $this->dateFin = \Carbon\Carbon::parse($this->projet->durer);

        $this->listenTaux();
    }

    // Méthode déclenchée lorsque le compte à rebours est terminé
    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du projet
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
        // $close = true; // Votre logique d'éligibilité ici

        // if ($close) {
        //     $this->dispatch('submittion', $close,);
        // }

    }

    #[On('echo:comments,CommentSubmittedTaux')]
    public function listenTaux()
    {
        $this->commentTauxList = CommentTaux::with('investisseur') // Assurez-vous que la relation est définie dans le modèle CommentTaux
            ->where('id_projet', $this->projet->id)
            ->orderBy('taux', 'asc') // Trier par le champ 'taux' en ordre croissant
            ->get();
    }
    public function updatedMontant()
    {
        // Vérifier si le montant saisi dépasse le solde
        $this->insuffisant = !empty($this->montant) && $this->montant > $this->solde;
    }


    public function confirmer()
    {
        // Log du début de la méthode
        Log::info('Démarrage de la méthode confirmer pour l\'utilisateur ID: ' . Auth::id());

        // Vérifier que le montant est valide, non vide, numérique et supérieur à zéro
        $montant = floatval($this->montant);

        if (empty($this->montant) || !is_numeric($montant) || $montant <= 0) {
            Log::warning('Montant invalide saisi par l\'utilisateur ID: ' . Auth::id() . ', Montant: ' . $this->montant);
            session()->flash('error', 'Veuillez saisir un montant valide.');
            return;
        }

        // Récupérer le projet
        $projet = $this->projet;

        // Vérifiez si le projet et le demandeur existent
        if (!$projet || !$projet->demandeur || !$projet->demandeur->id) {
            Log::error('Projet ou demandeur introuvable pour l\'utilisateur ID: ' . Auth::id());
            session()->flash('error', 'Le projet ou le demandeur est introuvable.');
            return;
        }

        // Log du projet et demandeur
        Log::info('Projet trouvé, ID du projet: ' . $projet->id . ', ID du demandeur: ' . $projet->demandeur->id);

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
            $ajoumontant = AjoutMontant::create([
                'montant' => $montant,
                'id_invest' => Auth::id(),
                'id_emp' => $projet->demandeur->id,
                'id_projet' => $projet->id,
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

            // Mettre à jour ou créer un enregistrement dans la table CFA du demandeur
            $cfa = Cfa::where('id_wallet', $walletDemandeur->id)->first();
            if ($cfa) {
                $cfa->Solde += $montant;
                $cfa->save();
                Log::info('Solde CFA mis à jour pour le demandeur ID: ' . $projet->id_user . ', Nouveau solde CFA: ' . $cfa->Solde);
            }

            // Générer une référence de transaction
            $reference_id = $this->generateIntegerReference();
            Log::info('Référence de transaction générée: ' . $reference_id);

            // Créer deux transactions
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Envoie', $montant, $reference_id, 'financement de crédit d\'achat', 'effectué');
            $this->createTransaction(Auth::id(), $this->projet->id_user, 'Reception', $montant, $reference_id, 'réception de financement de crédit d\'achat', 'effectué');
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

        // Message de succès
        session()->flash('success', 'Le montant a été ajouté avec succès.');

        // Réinitialiser le montant saisi et les indicateurs
        $this->montant = '';
        $this->insuffisant = false;

        // Rafraîchir les propriétés du composant
        $this->sommeInvestie = AjoutMontant::where('id_projet', $this->projet->id)->sum('montant');
        $this->sommeRestante = $this->projet->montant - $this->sommeInvestie;
        $this->pourcentageInvesti = ($this->sommeInvestie / $this->projet->montant) * 100;

        // Log de mise à jour des sommes investies
        Log::info('Somme investie mise à jour pour le projet ID: ' . $this->projet->id . ', Somme investie: ' . $this->sommeInvestie);

        // Mettre à jour le nombre d'investisseurs distincts
        $this->nombreInvestisseursDistinct = AjoutMontant::where('id_projet', $this->projet->id)
            ->distinct()
            ->count('id_invest');

        // Vérifier si le montant du projet est atteint
        $this->montantVerifie = AjoutMontant::where('id_projet', $this->projet->id)
            ->where('montant', $this->projet->montant)
            ->exists();

        // Log final
        Log::info('Montant vérifié pour le projet ID: ' . $this->projet->id . ', Montant atteint: ' . ($this->montantVerifie ? 'Oui' : 'Non'));
    }


    public function commentForm()
    {
        // Validation du champ tauxTrade
        $this->validate([
            'tauxTrade' => 'required|numeric|min:0',
        ]);

        // Vérification de l'objet projet
        if (!$this->projet || !$this->projet->id) {
            session()->flash('error', 'Le projet est introuvable.');
            return;
        }

        $user = auth()->user();

        $userWallet = Wallet::where('user_id', $user->id)->first();
        $coiWallet = Coi::where('id_wallet', $userWallet->id)->first();
        if (!$coiWallet  || $coiWallet->Solde < $this->projet->montant) {
            session()->flash('error', 'Votre solde est insuffisant pour soumettre une offre. Montant requis : ' . $this->projet->montant . ' CFA.');
            return;
        }

        // Insérer dans la table commentTaux
        try {
            $commentTaux = CommentTaux::create([
                'taux' => $this->tauxTrade,
                'id_invest' => auth()->id(),
                'id_emp' => $this->projet->id_user,
                'id_projet' => $this->projet->id,
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
            ->where('id_projet', $this->projet->id)
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

    public function joursRestants()
    {
        $dateActuelle = now();
        $joursRestants = $dateActuelle->diffInDays($this->dateFin);
        return max(0, $joursRestants); // Retournez 0 si le projet est déjà terminé
    }

    public function render()
    {
        return view('livewire.detail-projet', [
            'joursRestants' => $this->joursRestants(),
            'images' => $this->images,
            'nombreInvestisseurs' => $this->nombreInvestisseursDistinct,
            'sommeRestante' => $this->sommeRestante,
            'pourcentageInvesti' => $this->pourcentageInvesti,
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\AchatDirect;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class LivraisonAchatdirect extends Component
{
    public $notification;
    public $id;
    public $comments = [];
    public $oldestComment;
    public $oldestCommentDate;
    public $serverTime;
    public $quantite;
    public $idProd;
    public $userSender;
    public $code_livr;
    public $prixProd;
    public $id_trader;
    public $prixTrade;
    public $user;
    public $commentCount;
    public $produit, $nombreParticipants, $achatdirect;
    public $Valuecode_unique;

    public function mount($id)
    {
        // Récupérer la notification par son ID ou échouer
        $this->notification = DatabaseNotification::findOrFail($id);

        // Récupérer les données nécessaires depuis la notification
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->produit = ProduitService::find($this->idProd);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);

        // Assurez-vous que $this->achatdirect existe avant d'accéder à ses propriétés
        if (!$this->achatdirect) {
            throw new \Exception("Achat direct introuvable pour l'ID: " . $this->notification->data['achat_id']);
        }

        // Déterminer la valeur de $Valuecode_unique
        $this->Valuecode_unique = $this->notification->type_achat == 'appelOffreGrouper'
            ? ($this->notification->data['code_unique'] ?? null)
            : $this->achatdirect->code_unique;

        // Récupérer le plus ancien commentaire associé à ce code unique
        $this->oldestComment = Countdown::where('code_unique', $this->Valuecode_unique)
            ->where('notified', false)
            ->whereNotNull('start_time')
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment
            ? $this->oldestComment->created_at->toIso8601String()
            : null;

        // Écouter les messages en temps réel (Livewire/AlpineJS ou autre)
        $this->listenForMessage();
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->Valuecode_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();


        // Assurez-vous que 'comments' est bien une collection avant d'appliquer pluck()
        if ($this->comments instanceof \Illuminate\Database\Eloquent\Collection) {
            $this->commentCount = $this->comments->count();
            // Obtenir le nombre d'investisseurs distincts
            $this->nombreParticipants = $this->comments->pluck('user.id')->unique()->count();
        } else {
            // Si ce n'est pas une collection, gestion d'erreur ou traitement spécifique
            $this->nombreParticipants = 0;
        }
    }

    protected $listeners = ['compteReboursFini'];

    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du demandeCredit
        $this->achatdirect->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            )
        ]);
    }
    public function commentFormLivr()
    {
        // Valider les données
        $validatedData = $this->validate([
            'prixTrade' => 'required|numeric'
        ]);

        DB::beginTransaction();
        try {

            // Créer un commentaire
            $comment = Comment::create([
                'prixTrade' => $validatedData['prixTrade'],
                'code_unique' => $this->Valuecode_unique,
                'id_trader' => Auth::id(),
                'quantiteC' => $this->achatdirect->quantité,
                'id_prod' => $this->achatdirect->idProd,
                'prixProd' => $this->produit->prix,
                'id_sender' => json_encode($this->achatdirect->userTrader),
            ]);

            // Réinitialiser le champ du formulaire
            $this->reset(['prixTrade']);

            broadcast(new CommentSubmitted($validatedData['prixTrade'],  $comment->id))->toOthers();

            // Vérifier si 'code_unique' existe dans les données de notification
            $this->comments = Comment::with('user')
                ->where('code_unique', $this->Valuecode_unique)
                ->whereNotNull('prixTrade')
                ->orderBy('prixTrade', 'asc')
                ->get();

            // Vérifier si un compte à rebours est déjà en cours pour cet code unique
            $existingCountdown = Countdown::where('code_unique', $this->Valuecode_unique)
                ->where('notified', false)
                ->orderBy('start_time', 'desc')
                ->first();

            if (!$existingCountdown) {
                // Créer un nouveau compte à rebours s'il n'y en a pas en cours
                Countdown::create([
                    'user_id' => Auth::id(),
                    'userSender' => $this->achatdirect->userSender,
                    'start_time' => now(),
                    'difference' => 'ad',
                    'code_unique' => $this->Valuecode_unique,
                    'id_achat' => $this->achatdirect->id,
                ]);
                // Émettre l'événement 'CountdownStarted' pour démarrer le compte à rebours en temps réel
                broadcast(new OldestCommentUpdated(now()->toIso8601String()));
                $this->dispatch('OldestCommentUpdated', now()->toIso8601String());
            }

            // Committer la transaction
            DB::commit();
            // Optionnel: Ajouter une notification ou un message de succès
            session()->flash('message', 'Commentaire sur le taux ajouté avec succès.');
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.livraison-achatdirect');
    }
}

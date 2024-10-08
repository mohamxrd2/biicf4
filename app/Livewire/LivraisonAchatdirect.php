<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
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
    public $produit;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->code_livr = $this->notification->data['code_livr'] ?? null;
        $this->quantite = $this->notification->data['quantite'] ?? $this->notification->data['quantity'] ?? $this->notification->data['quantites'] ?? null;
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->userSender = $this->notification->data['userSender'] ?? null;
        $this->id_trader = Auth::user()->id ?? null;
        $this->prixProd = $this->notification->data['prixProd'] ?? null;
        $this->user = Auth::user()->id ?? null;
        $this->produit = ProduitService::find($this->idProd);

        // Vérifier si 'code_unique' existe dans les données de notification
        $codeUnique = $this->notification->data['code_livr'];
        $comments = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();
        foreach ($comments as $comment) {
            $this->commentsend($comment);
            Log::info('État actuel de la liste des commentaires', ['comments' => $this->comments]);
        }

        $this->commentCount = $comments->count();

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Countdown::where('code_unique', $codeUnique)
            ->whereNotNull('start_time')
            ->orderBy('created_at', 'asc')
            ->first();

        // Initialiser la variable pour la date du plus ancien commentaire
        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at->toIso8601String() : null;
        $this->serverTime = Carbon::now()->toIso8601String();
    }
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function commentFormLivr()
    {

        // Valider les données
        $validatedData = $this->validate([
            'code_livr' => 'required|string',
            'quantite' => 'required|numeric',
            'idProd' => 'required|numeric',
            'userSender' => 'required|numeric',
            'id_trader' => 'required|numeric',
            'prixProd' => 'required|numeric',
            'prixTrade' => 'required|numeric'
        ]);

        // Créer un commentaire
        $comment = Comment::create([
            'prixTrade' => $validatedData['prixTrade'],
            'code_unique' => $validatedData['code_livr'],
            'id_trader' => Auth::id(),
            'quantiteC' => $validatedData['quantite'],
            'id_prod' => $validatedData['idProd'],
            'prixProd' => $validatedData['prixProd'],
            'id_sender' => json_encode($this->notification->data['id_trader']), // Si la colonne est de type JSON (ce qui est rare pour une ID)
            'date_tot' => $this->notification->data['dateTot'],
            'date_tard' => $this->notification->data['dateTard'],
        ]);

        $this->commentsend($comment);

        broadcast(new CommentSubmitted($validatedData['prixTrade'],  $comment->id))->toOthers();


        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $validatedData['code_livr'])
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $this->userSender,
                'start_time' => now(),
                'difference' => 'ad',
                'code_unique' => $validatedData['code_livr'],
            ]);
            // Émettre l'événement 'CountdownStarted' pour démarrer le compte à rebours en temps réel
            broadcast(new OldestCommentUpdated(now()->toIso8601String()));
            $this->dispatch('OldestCommentUpdated', now()->toIso8601String());
        }

        // Afficher un message de succès
        session()->flash('success', 'Commentaire créé avec succès!');

        // Réinitialiser le champ du formulaire
        $this->reset(['prixTrade']);

        // Émettre un événement pour notifier les autres utilisateurs
        $this->dispatch('form-submitted', 'prix soumis avec succes');
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage($event)
    {
        // Déboguer pour vérifier la structure de l'événement
        // dd($event);
        Log::info('Événement reçu', ['event' => $event]);

        // Récupérer les données de l'événement
        $commentId = $event['commentId'] ?? null;

        if ($commentId) {
            Log::info('Commentaire trouvé par ID', ['comment' => $commentId]);

            // Récupérer le commentaire par ID
            $comment = Comment::with('user')->find($commentId);

            if ($comment) {
                // Ajouter le nouveau commentaire à la liste
                $this->commentsend($comment);
            } else {
                // Gérer le cas où le commentaire n'existe pas
                Log::error('Commentaire non trouvé', ['commentId' => $commentId]);
            }
        } else {
            // Gestion des erreurs si l'ID du commentaire n'est pas fourni
            Log::error('ID du commentaire manquant dans l\'événement', ['event' => $event]);
        }
    }

    public function commentsend($comment)
    {
        if ($comment) {
            $this->comments[] = [
                'prix' => $comment->prixTrade,
                'commentId' => $comment->id,
                'nameUser' => $comment->user->name,
                'photoUser' => $comment->user->photo,
            ];
            Log::info('Commentaire ajouté', ['comment' => $comment->user->name]);
        }
    }

    // #[On('refreshCountdown')]
    // public function refreshPage()
    // {
    //     // Logique pour rafraîchir les données ou la page.
    //     $this->mount($this->id); // Re-monte les données initiales
    //     $this->render(); // Ou appelez simplement render() si cela suffit
    // }

    public function render()
    {
        return view('livewire.livraison-achatdirect');
    }
}

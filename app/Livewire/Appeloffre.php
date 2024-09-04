<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Appeloffre extends Component
{
    public $notification;
    public $id;
    public $comments = [];
    public $oldestComment;
    public $oldestCommentDate;
    public $serverTime;
    public $quantite;
    public $idProd;
    public $idsender;
    public $code_unique;
    public $prixProd;
    public $localite;
    public $specificite;
    public $nameprod;
    public $quantiteC;
    public $difference;
    public $id_trader;
    public $prixTrade;
    public $namefourlivr;

    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->code_unique = $this->notification->data['code_unique'] ?? null;
        $this->prixProd = $this->notification->data['prixProd'] ?? null;
        $this->specificite = $this->notification->data['specificity'] ?? null;
        $this->localite = $this->notification->data['localite'] ?? null;
        $this->nameprod = $this->notification->data['productName'] ?? null;
        $this->idsender = $this->notification->data['id_sender'] ?? null;
        $this->difference = $this->notification->data['difference'] ?? null;
        $this->quantiteC = $this->notification->data['quantity'] ?? null;
        $this->id_trader = Auth::user()->id ?? null;
        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);



        // Vérifier si 'code_unique' existe dans les données de notification
        $codeUnique = $this->notification->data['code_unique']
            ?? $this->notification->data['code_livr']
            ?? $this->notification->data['Uniquecode'] ?? null;
        $comments = Comment::with('user')
            ->where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();
        foreach ($comments as $comment) {
            $this->commentsend($comment);
        }

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Comment::where('code_unique', $codeUnique)
            ->whereNotNull('prixTrade')
            ->orderBy('created_at', 'asc')
            ->first();

        // Initialiser la variable pour la date du plus ancien commentaire
        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at->toIso8601String() : null;
        $this->serverTime = Carbon::now()->toIso8601String();
    }

    public function commentForm()
    {
        try {
            // Récupérer l'utilisateur authentifié
            $this->validate([
                'code_unique' => 'required|string',
                'quantiteC' => 'required|numeric',
                'difference' => 'required|string',
                'idsender' => 'required|numeric',
                'id_trader' => 'required|numeric',
                'nameprod' => 'required|string',
                'localite' => 'required|string',
                'specificite' => 'nullable|string',
                'prixTrade' => 'required|numeric',

            ]);

            $comment = Comment::create([
                'localite' => $this->notification->data['localite'],
                'specificite' => $this->specificite ?? 'Take Away',
                'prixTrade' => $this->prixTrade,
                'id_sender' => json_encode($this->idsender),
                'nameprod' => $this->nameprod,
                'code_unique' => $this->code_unique,
                'type' => $this->notification->type_achat ?? null,
                'id_trader' => $this->id_trader,
                'quantiteC' => $this->quantiteC,
            ]);

            $this->commentsend($comment);

            broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();


            // Vérifier si un compte à rebours est déjà en cours pour cet code unique
            $existingCountdown = Countdown::where('code_unique', $this->code_unique)
                ->where('notified', false)
                ->orderBy('start_time', 'desc')
                ->first();

            if (!$existingCountdown) {
                // Créer un nouveau compte à rebours s'il n'y en a pas en cours
                Countdown::create([
                    'user_id' => $this->id_trader,
                    'userSender' => $this->namefourlivr,
                    'start_time' => now(),
                    'code_unique' => $this->code_unique,
                    'difference' => $this->difference,
                ]);
            }

            session()->flash('success', 'Commentaire créé avec succès!');

            $this->reset(['prixTrade']);
        } catch (Exception $e) {
            // Gérer l'exception, enregistrer l'erreur dans les logs et afficher un message d'erreur
            Log::error('Erreur lors de la soummission: ' . $e->getMessage());

            // Vous pouvez ajouter un retour ou une redirection avec un message d'erreur
            return back()->with('error', 'Une erreur s\'est produite lors du refus de la proposition.');
        }
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage($event)
    {
        // Déboguer pour vérifier la structure de l'événement
        // dd($event);

        // Récupérer les données de l'événement
        $commentId = $event['commentId'] ?? null;

        if ($commentId) {
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
        }
    }


    public function render()
    {
        return view('livewire.appeloffre');
    }
}

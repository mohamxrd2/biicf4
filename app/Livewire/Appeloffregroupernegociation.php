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

class Appeloffregroupernegociation extends Component
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
    public $id_sender;

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

        $this->idsender = $this->notification->data['id_sender'] ?? null;

        if (array_key_exists('id_sender', $this->notification->data)) {
            $idSender = $this->notification->data['id_sender'];

            if (is_array($idSender)) {
                //If $idSender is already an array, assign it directly
                $this->id_sender = $idSender;
            } else {
                // If $idSender is a string, use explode to convert it to an array
                $this->id_sender = explode(',', $idSender);
            }
        } else {
            //Handle the case where 'id_sender' does not exist
            $this->id_sender = null; // or any other default value you prefer
        }
    }

    public function commentFormGroupe()
    {
        // Récupérer l'utilisateur authentifié
        $this->validate([
            'code_unique' => 'required|string',
            'quantiteC' => 'required|numeric',
            'prixTrade' => 'required|numeric',
            'id_sender' => 'required|array',
            'id_sender.*' => 'numeric',
            'id_trader' => 'required|numeric',
            'nameprod' => 'required|string',
            'difference' => 'required|string',
            'localite' => 'required|string',
            'specificite' => 'nullable|string',

        ]);

        $comment = Comment::create([
            'localite' => $this->notification->data['localite'],
            'specificite' => $this->specificite,
            'prixTrade' => $this->prixTrade,
            'id_sender' => json_encode($this->id_sender),
            'nameprod' => $this->nameprod,
            'code_unique' => $this->code_unique,
            'id_trader' => $this->id_trader,
            'quantiteC' => $this->quantiteC,
            'date_tot' => $this->notification->data['dateTot'],
            'date_tard' => $this->notification->data['dateTard'],
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
                // 'userSender' => json_encode($this->id_sender),
                'start_time' => now(),
                'code_unique' => $this->code_unique,
                'difference' => $this->difference,
            ]);
        }

        session()->flash('success', 'Commentaire créé avec succès!');

        $this->reset(['prixTrade']);
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
        return view('livewire.appeloffregroupernegociation');
    }
}

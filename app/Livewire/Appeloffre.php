<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\AppelOffreUser;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public $prixProd;
    public $localite;
    public $specificite;
    public $nameprod;
    public $quantiteC;
    public $difference;
    public $id_trader;
    public $prixTrade;
    public $namefourlivr;
    public $appeloffre;
    public $commentCount;
    public $nombreParticipants;
    public $produit;
    public $code_unique;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appelOffre']);
        $this->id_trader = Auth::user()->id ?? null;
        $this->produit = ProduitService::where('reference', $this->appeloffre->reference)->first();

        // Vérifier si 'code_unique' existe dans les données de notification
        $this->code_unique = $this->notification->data['code_unique'];

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Countdown::where('code_unique', $this->code_unique)
            ->whereNotNull('start_time')
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at->toIso8601String() : null;
        $this->serverTime = Carbon::now()->toIso8601String();
        $this->listenForMessage();
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->appeloffre->code_unique)
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

    // protected $listeners = ['compteReboursFini'];
    // public function compteReboursFini()
    // {
    //     // Mettre à jour l'attribut 'finish' du demandeCredit
    //     $this->achatdirect->update([
    //         'count' => true,
    //         $this->dispatch(
    //             'formSubmitted',
    //             'Temps écoule, Négociation terminé.'
    //         )
    //     ]);
    // }

    public function commentFormLivr()
    {

        // Récupérer l'utilisateur authentifié
        $this->validate([
            'prixTrade' => 'required|numeric',
        ]);

        // if ($this->prixTrade < $this->appeloffre->lowestPricedProduct) {
        //     session()->flash('error', 'Commentaire créé avec succès!');
        //     return;
        // }
        DB::beginTransaction();

        try {


            $comment = Comment::create([
                'prixTrade' => $this->prixTrade,
                'code_unique' => $this->code_unique,
                'id_trader' => Auth::id(),
                'quantiteC' => $this->appeloffre->quantity,
                'id_sender' => json_encode($this->appeloffre->prodUsers),
            ]);

            broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();

            // Vérifier si 'code_unique' existe dans les données de notification
            $this->comments = Comment::with('user')
                ->where('code_unique', $this->appeloffre->code_unique)
                ->whereNotNull('prixTrade')
                ->orderBy('prixTrade', 'asc')
                ->get();

            // Vérifier si un compte à rebours est déjà en cours pour cet code unique
            $existingCountdown = Countdown::where('code_unique', $this->code_unique)
                ->where('notified', false)
                ->orderBy('start_time', 'desc')
                ->first();

            if (!$existingCountdown) {
                // Créer un nouveau compte à rebours s'il n'y en a pas en cours
                Countdown::create([
                    'user_id' => $this->id_trader,
                    'userSender' => null,
                    'start_time' => now(),
                    'code_unique' => $this->code_unique,
                    'difference' => 'appOffre',
                    'id_appeloffre' => $this->appeloffre->id,
                ]);
                // Émettre l'événement 'CountdownStarted' pour démarrer le compte à rebours en temps réel
                broadcast(new OldestCommentUpdated(now()->toIso8601String()));
                $this->dispatch('OldestCommentUpdated', now()->toIso8601String());
            }

            session()->flash('success', 'Commentaire créé avec succès!');

            DB::commit();

            $this->reset(['prixTrade']);
        } catch (Exception $e) {
            // Gérer l'exception, enregistrer l'erreur dans les logs et afficher un message d'erreur
            Log::error('Erreur lors de la soummission: ' . $e->getMessage());

            // Vous pouvez ajouter un retour ou une redirection avec un message d'erreur
            return back()->with('error', 'Une erreur s\'est produite lors du refus de la proposition.');
        }
    }


    public function render()
    {
        return view('livewire.appeloffre');
    }
}

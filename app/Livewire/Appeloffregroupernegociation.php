<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\AppelOffreGrouper;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
    public $appeloffregrp;
    public $commentCount;
    public $nombreParticipants;
    public $time;
    public $error;
    public $timestamp;
    public $prixLePlusBas;
    public $offreIniatiale;
    protected $recuperationTimer;
    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {
        $this->time = $this->recuperationTimer->getTime();
        $this->error = $this->recuperationTimer->error;
        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = Carbon::createFromTimestamp($seconds);

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->id_trader = Auth::user()->id ?? null;

        $this->appeloffregrp = AppelOffreGrouper::find($this->notification->data['id_appelGrouper']);

        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Countdown::where('code_unique', $this->appeloffregrp->codeunique)
            ->whereNotNull('start_time')
            ->where('notified', false)
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ?
            Carbon::parse($this->oldestComment->start_time)->toIso8601String()
            : null;
        $this->listenForMessage();
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->appeloffregrp->codeunique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();


        $this->prixLePlusBas = Comment::where('code_unique', $this->code_unique)
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        $this->offreIniatiale = Comment::where('code_unique', $this->code_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->first(); // Récupère le premier commentaire trié

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
        $this->appeloffregrp->update([
            'count2' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            )
        ]);
    }

    public function commentFormLivr()
    {

        // Récupérer l'utilisateur authentifié
        $this->validate([
            'prixTrade' => 'required|numeric',
        ]);

        if ($this->prixTrade <  $this->appeloffregrp->lowestPricedProduct) {
            session()->flash('error', 'prix trop haut!');
            return;
        }

        DB::beginTransaction();

        try {


            $comment = Comment::create([
                'prixTrade' => $this->prixTrade,
                'code_unique' => $this->appeloffregrp->codeunique,
                'id_trader' => Auth::id(),
                'quantiteC' => $this->appeloffregrp->quantity,
                'id_sender' => json_encode($this->appeloffregrp->prodUsers),
            ]);

            broadcast(new CommentSubmitted($this->prixTrade,  $comment->id))->toOthers();



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
        return view('livewire.appeloffregroupernegociation');
    }
}

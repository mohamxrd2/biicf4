<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\DebutDeNegociation;
use App\Events\OldestCommentUpdated;
use App\Models\AppelOffreUser;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use DateTime;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Bt51\NTP\Socket;
use Bt51\NTP\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
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
    public $ServerTime;


    public $time;
    public $error;
    public $prixLePlusBas;
    public $offreIniatiale;
    public $timestamp;
    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {
        // Récupération de l'heure via le service
        $this->time = $this->recuperationTimer->getTime();
        $this->error = $this->recuperationTimer->error;
        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = Carbon::createFromTimestamp($seconds);

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appelOffre']);
        $this->id_trader = Auth::user()->id ?? null;
        $this->produit = ProduitService::where('reference', $this->appeloffre->reference)->first();

        // Vérifier si 'code_unique' existe dans les données de notification
        $this->code_unique = $this->notification->data['code_unique'];

        // Récupérer le commentaire le plus ancien avec code_unique et start_time non nul
        $this->oldestComment = Countdown::where('code_unique', $this->code_unique)
            ->whereNotNull('start_time')
            ->where('notified', false)
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment
            ? Carbon::parse($this->oldestComment->start_time)->toIso8601String()
            : null;

        // Debug pour vérifier le résultat
        // dd($this->oldestCommentDate);


        $this->listenForMessage();
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->code_unique)
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
        $this->appeloffre->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            )

        ]);
        $this->dispatch(
            'formSubmitted',
            'Temps écoule, Négociation terminé.'
        );
    }

    public function commentFormLivr()
    {

        // Récupérer l'utilisateur authentifié
        $this->validate([
            'prixTrade' => 'required|numeric',
        ]);

        if ($this->prixTrade > $this->appeloffre->lowestPricedProduct) {
            session()->flash('error', 'Prix trop elevee!');
            return;
        }
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
            $this->listenForMessage();

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

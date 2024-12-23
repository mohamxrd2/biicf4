<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\OffreGroupe;
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

class Enchere extends Component
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
    public $commentCount;
    public $offgroupe;
    public $produit, $nombreParticipants, $achatdirect;
    public $time;
    public $error;
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
        // Récupération de l'heure via le service
        $this->time = $this->recuperationTimer->getTime();
        $this->error = $this->recuperationTimer->error;

        $this->notification = DatabaseNotification::findOrFail($id);

        $this->id_trader = Auth::user()->id ?? null;
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->produit = ProduitService::find($this->idProd);
        $this->offgroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->first();


        // Récupérer le commentaire le plus ancien avec code_unique et prixTrade non nul
        $this->oldestComment = Countdown::where('code_unique', $this->notification->data['code_unique'])
            ->whereNotNull('start_time')
            ->where('notified', false)
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment ? $this->oldestComment->created_at->toIso8601String() : null;
        $this->listenForMessage();
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->notification->data['code_unique'])
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'desc')
            ->get();

        $this->prixLePlusBas = Comment::where('code_unique', $this->notification->data['code_unique'])
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        $this->offreIniatiale = Comment::where('code_unique', $this->notification->data['code_unique'])
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
        $this->offgroupe->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Négociation terminé.'
            )
        ]);
    }

    public function commentoffgroup()
    {
        // Valider les données
        $validatedData = $this->validate([
            'prixTrade' => 'required|numeric'
        ]);

        try {

            // Créer un commentaire
            $comment = Comment::create([
                'prixTrade' => $validatedData['prixTrade'],
                'code_unique' => $this->notification->data['code_unique'],
                'id_trader' => Auth::id(),
                'id_prod' => $this->produit->id,
            ]);

            broadcast(new CommentSubmitted($validatedData['prixTrade'],  $comment->id));
            $this->listenForMessage();

            $this->reset(['prixTrade']);
        } catch (Exception $e) {
            // dd($e)->getMessage();
            // En cas d'erreur, redirection avec un message d'erreur
            return redirect()->back()->with('error', 'Erreur lors de la soumission de l\'offre: ' . $e->getMessage());
        }
    }




    public function render()
    {
        return view('livewire.enchere');
    }
}

<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Models\AppelOffreGrouper;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Models\userquantites;
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
    public $sumquantite;
    public $appelOffreGroupcount;
    protected $recuperationTimer;
    public $lastActivity;
    public $isNegociationActive;

    protected $listeners = ['negotiationEnded' => '$refresh'];

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }
    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->id_trader = Auth::user()->id ?? null;

        $this->appeloffregrp = AppelOffreGrouper::find($this->notification->data['id_appelGrouper']);


        $this->listenForMessage();

        $this->sumquantite = userquantites::where('code_unique', $this->appeloffregrp->codeunique)
            ->sum('quantite');
        $this->appelOffreGroupcount = userquantites::where('code_unique', $this->appeloffregrp->codeunique)->distinct('user_id')->count('user_id');
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->notification->data['code_livr'])
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();


        $this->prixLePlusBas = Comment::where('code_unique', $this->notification->data['code_livr'])
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        $this->offreIniatiale = Comment::where('code_unique', $this->notification->data['code_livr'])
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


    public function commentFormLivr()
    {

        // Vérifier si la négociation est terminée
        if ($this->appeloffregrp->count2) {
            $this->dispatch(
                'formSubmitted',
                'La négociation est terminée. Vous ne pouvez plus soumettre d\'offres.'
            );
            return;
        }

        // Récupérer d'abord l'offre initiale pour la validation
        $offreInitiale = Comment::where('code_unique', $this->notification->data['code_livr'])
            ->whereNotNull('prixTrade')
            ->orderBy('created_at', 'asc')
            ->first();

        // Valider les données avec une règle personnalisée
        $validatedData = $this->validate([
            'prixTrade' => [
                'required',
                'numeric',
                function ($attribute, $value, $fail) use ($offreInitiale) {
                    if ($offreInitiale && $value > $offreInitiale->prixTrade) {
                        $fail("Le prix proposé ne peut pas être supérieur au prix initial de " . $offreInitiale->prixTrade);
                    }
                }
            ]
        ]);

        DB::beginTransaction();

        try {


            $comment = Comment::create([
                'prixTrade' => $this->prixTrade,
                'code_unique' => $this->notification->data['code_livr'],
                'id_trader' => Auth::id(),
                'quantiteC' => $this->appeloffregrp->quantity,
                'id_sender' => json_encode($this->appeloffregrp->prodUsers),
            ]);

            broadcast(new CommentSubmitted($this->prixTrade,  $comment->id));
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
        return view('livewire.appeloffregroupernegociation');
    }
}

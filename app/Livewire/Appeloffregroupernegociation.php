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
    public $comments = [], $oldestComment, $oldestCommentDate, $serverTime, $quantite, $idProd, $idsender, $code_unique;
    public $prixProd, $localite, $specificite, $nameprod, $quantiteC, $difference, $id_trader, $prixTrade, $namefourlivr, $id_sender;
    public $appeloffregrp, $commentCount, $nombreParticipants, $time, $error, $timestamp, $prixLePlusBas, $offreIniatiale, $sumquantite, $appelOffreGroupcount;
    protected $recuperationTimer;
    public $lastActivity;
    public $isNegociationActive;
    public $isLoading = false;
    public $errorMessage = null;
    public $successMessage = null;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }

    protected $listeners = [
        'negotiationEnded' => '$refresh'
    ];

    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->id_trader = Auth::user()->id ?? null;
        $this->appeloffregrp = AppelOffreGrouper::find($this->notification->data['id_appelGrouper']);

        // Vérifier si 'code_unique' existe dans les données de notification
        $this->code_unique = $this->notification->data['code_livr'];

        $this->sumquantite = userquantites::where('code_unique', $this->appeloffregrp->codeunique)
            ->sum('quantite');
        $this->appelOffreGroupcount = userquantites::where('code_unique', $this->appeloffregrp->codeunique)->distinct('user_id')->count('user_id');


        $countdown = Countdown::where('code_unique', $this->code_unique)
            ->where('is_active', false)
            ->first();

        if ($countdown && !$this->appeloffregrp->count) {
            $this->appeloffregrp->update(['count' => true]);
        }

        $this->offreIniatiale  = $this->appeloffregrp->lowestPricedProduct;

        $this->listenForMessage();
    }

    #[On('echo:comments.{code_unique},CommentSubmitted')]
    public function listenForMessage()
    {
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->notification->data['code_livr'])
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();

        $this->prixLePlusBas = Comment::where('code_unique', $this->notification->data['code_livr'])
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        $this->isNegociationActive = !$this->appeloffregrp->count;

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

    // Règles de validation
    protected function rules()
    {
        return [
            'prixTrade' => [
                'required',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) {
                    // Vérifier que le prix ne dépasse pas le prix initial le plus bas
                    if ($value > $this->offreIniatiale) {
                        $fail("Le prix proposé doit être inférieur ou égal au prix initial de " . $this->offreIniatiale);
                    }

                    $dernierePlusBasseOffre = Comment::where('code_unique', $this->code_unique)
                        ->whereNotNull('prixTrade')
                        ->orderBy('prixTrade', 'asc')
                        ->first();

                    if ($dernierePlusBasseOffre && $value >= $dernierePlusBasseOffre->prixTrade) {
                        $fail("Le prix proposé doit être inférieur au prix actuel de " . $dernierePlusBasseOffre->prixTrade);
                    }
                }
            ]
        ];
    }

    // Messages personnalisés de validation
    protected function messages()
    {
        return [
            'prixTrade.required' => 'Veuillez saisir un prix.',
            'prixTrade.numeric' => 'Le prix doit être un nombre valide.',
            'prixTrade.min' => 'Le prix doit être supérieur à zéro.'
        ];
    }
    public function soumissionDePrix()
    {

        // Vérifier si la négociation est terminée
        if ($this->appeloffregrp->count) {
            $this->dispatch(
                'formSubmitted',
                'La négociation est terminée. Vous ne pouvez plus soumettre d\'offres.'
            );
            return;
        }

        // Activer l'état de chargement
        $this->isLoading = true;
        $this->errorMessage = null;
        $this->successMessage = null;
        
        try {
            // Transaction de base de données
            $comment = DB::transaction(function () {
                // Vérifier et verrouiller l'appel d'offre
                $appeloffregrp = AppelOffreGrouper::where('codeunique', $this->code_unique)
                    ->lockForUpdate()
                    ->first();

                // Validation des données
                $validatedData = $this->validate();

                $comment = Comment::create([
                    'prixTrade' => $this->prixTrade,
                    'code_unique' => $this->notification->data['code_livr'],
                    'id_trader' => Auth::id(),
                    'quantiteC' => $this->appeloffregrp->quantity,
                    'id_sender' => json_encode($this->appeloffregrp->prodUsers),
                ]);

                event(new CommentSubmitted($this->code_unique, $comment));


                return $comment;
            }, attempts: 3); // Nombre de tentatives de transaction

            // Actualiser les données
            $this->listenForMessage();
            $this->reset('prixTrade');

            // Message de succès
            $this->successMessage = "Votre offre a été soumise avec succès.";
        } catch (Exception $e) {
            // Gestion des autres erreurs
            $this->errorMessage = "Une erreur est survenue : " . $e->getMessage();
            Log::error('Erreur de soumission', [
                'message' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
        } finally {
            // Désactiver l'état de chargement
            $this->isLoading = false;
        }
    }

    public function render()
    {
        return view('livewire.appeloffregroupernegociation');
    }
}

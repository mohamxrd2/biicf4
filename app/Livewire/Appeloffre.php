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
    public $oldestCommentDate, $serverTime, $quantite, $idProd, $idsender, $prixProd, $localite, $specificite;
    public $nameprod, $quantiteC, $difference, $id_trader, $prixTrade, $namefourlivr, $appeloffre, $commentCount, $nombreParticipants, $produit, $code_unique, $ServerTime;


    public $time, $error, $prixLePlusBas, $offreIniatiale, $timestamp;
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
        'negotiationEnded' => '$refresh',
    ];

    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appelOffre']);
        $this->id_trader = Auth::user()->id ?? null;
        $this->produit = ProduitService::where('reference', $this->appeloffre->reference)->first();

        // Vérifier si 'code_unique' existe dans les données de notification
        $this->code_unique = $this->notification->data['code_unique'];

        $countdown = Countdown::where('code_unique', $this->code_unique)
            ->where('is_active', false)
            ->first();

        if ($countdown && !$this->appeloffre->count) {
            $this->appeloffre->update(['count' => true]);
        }
        $this->offreIniatiale  = $this->appeloffre->lowestPricedProduct;

        $this->listenForMessage();
    }

    #[On('echo:comments.{code_unique},CommentSubmitted')]
    public function listenForMessage()
    {
        // Déboguer pour vérifier la structure de l'événement
        // Vérifier si 'code_unique' existe dans les données de notification
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->code_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();

        // Prix le plus bas
        $this->prixLePlusBas = Comment::where('code_unique', $this->code_unique)
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        $this->isNegociationActive = !$this->appeloffre->count;

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

    // Méthode de soumission de prix
    public function soumissionDePrix()
    {
        // Vérifier si la négociation est terminée
        if ($this->achatdirect->count) {
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
                $appeloffre = AppelOffreUser::where('code_unique', $this->code_unique)
                    ->lockForUpdate()
                    ->first();

                // Validation des données
                $validatedData = $this->validate();

                // Créer le commentaire
                $comment = Comment::create([
                    'prixTrade' => $this->prixTrade,
                    'code_unique' => $this->code_unique,
                    'id_trader' => Auth::id(),
                    'quantiteC' => $appeloffre->quantity,
                    'id_sender' => json_encode($appeloffre->prodUsers),
                ]);

                // Événement de soumission
                event(new CommentSubmitted($this->code_unique, $comment));

                return $comment;
            }, 3); // Nombre de tentatives de transaction

            // Actualiser les données
            $this->listenForMessage();
            $this->reset('prixTrade');

            // Message de succès
            $this->successMessage = "Votre offre a été soumise avec succès.";
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Gestion des erreurs de validation
            $this->errorMessage = $e->validator->errors()->first();
            Log::warning('Erreur de validation', [
                'errors' => $e->errors(),
                'user_id' => Auth::id()
            ]);
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
        return view('livewire.appeloffre');
    }
}

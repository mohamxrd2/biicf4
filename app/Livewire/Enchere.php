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
    public $comments = [], $oldestComment, $oldestCommentDate, $serverTime, $quantite, $idProd;
    public $idsender;
    public $code_unique, $prixProd, $localite, $specificite, $nameprod, $quantiteC, $difference, $id_trader, $prixTrade, $namefourlivr, $commentCount;
    public $produit, $nombreParticipants, $offgroupe, $time, $error, $lastActivity, $prixLePlusBas, $offreIniatiale, $isNegociationActive;
    public $isLoading = false;
    public $errorMessage = null;
    public $successMessage = null;

    protected $listeners = ['negotiationEnded' => '$refresh'];



    public function mount($id)
    {
        try {
            $this->notification = DatabaseNotification::findOrFail($id);
            $this->id_trader = Auth::user()->id ?? null;
            $this->idProd = $this->notification->data['idProd'] ?? null;

            if (!$this->idProd) {
                throw new Exception("ID du produit manquant");
            }
            $this->produit = ProduitService::findOrFail($this->idProd);
            $this->offgroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->firstOrFail();
            $this->code_unique = $this->notification->data['code_unique'];

            $countdown = Countdown::where('code_unique', $this->offgroupe->code_unique)
                ->where('is_active', false)
                ->first();
            if ($countdown && !$this->offgroupe->count) {
                $this->offgroupe->update(['count' => true]);
            }

            $this->offreIniatiale =  $this->produit->prix;

            $this->listenForMessage();
        } catch (Exception $e) {
            Log::error('Erreur dans le montage de l\'enchère', [
                'error' => $e->getMessage(),
                'notification_id' => $id
            ]);
            throw $e;
        }
    }

    #[On('echo:comments.{code_unique},CommentSubmitted')]
    public function listenForMessage()
    {

        $this->comments = Comment::with('user')
            ->where('code_unique',  $this->code_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'desc')
            ->get();

        $this->prixLePlusBas = Comment::where('code_unique',  $this->code_unique)
            ->whereNotNull('prixTrade')
            ->max('prixTrade');

        $this->isNegociationActive = !$this->offgroupe->count;

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
                    if ($value < $this->offreIniatiale) {
                        $fail("Le prix proposé doit être superieur ou égal au prix initial de " . $this->offreIniatiale);
                    }

                    $dernierePlusBasseOffre = Comment::where('code_unique', $this->code_unique)
                        ->whereNotNull('prixTrade')
                        ->orderBy('prixTrade', 'asc')
                        ->first();

                    if ($dernierePlusBasseOffre && $value <= $dernierePlusBasseOffre->prixTrade) {
                        $fail("Le prix proposé doit être superieur au prix actuel de " . $dernierePlusBasseOffre->prixTrade);
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
        if ($this->offgroupe->count) {
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

        // try {

            // Transaction de base de données
            $comment = DB::transaction(function () {
                // Vérifier et verrouiller l'appel d'offre
                $offgroupe = OffreGroupe::where('code_unique', $this->code_unique)
                    ->lockForUpdate()
                    ->first();
                // Validation des données
                $validatedData = $this->validate();

                $comment = Comment::create([
                    'prixTrade' => $validatedData['prixTrade'],
                    'code_unique' => $this->code_unique,
                    'id_trader' => Auth::id(),
                    'id_prod' => $this->produit->id,
                ]);

                event(new CommentSubmitted($this->code_unique, $comment));
                return $comment;
            }, attempts: 3); // Nombre de tentatives de transaction

            // Actualiser les données
            $this->listenForMessage();
            $this->reset('prixTrade');

            // Message de succès
            $this->successMessage = "Votre offre a été soumise avec succès.";
        // } catch (Exception $e) {
        //     // Gestion des autres erreurs
        //     $this->errorMessage = "Une erreur est survenue : " . $e->getMessage();
        //     Log::error('Erreur de soumission', [
        //         'message' => $e->getMessage(),
        //         'user_id' => Auth::id()
        //     ]);
        // } finally {
        //     // Désactiver l'état de chargement
        //     $this->isLoading = false;
        // }
    }

    public function render()
    {
        return view('livewire.enchere', [
            'errors' => $this->getErrorBag()
        ]);
    }
}

<?php

namespace App\Livewire;

use App\Events\CommentSubmitted;
use App\Events\OldestCommentUpdated;
use App\Events\ServerTimeUpdated;
use App\Models\AchatDirect;
use App\Models\Comment;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Models\userquantites;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class LivraisonAchatdirect extends Component
{
    public $notification, $id, $comments = [], $oldestComment, $oldestCommentDate, $serverTime;

    public $commentCount, $produit, $nombreParticipants, $achatdirect, $Valuecode_unique, $prixLePlusBas,
        $offreIniatiale, $time, $error, $timestamp, $lastActivity, $isNegociationActive, $usersLocations,
        $quantite, $idProd, $userSender, $code_livr, $prixProd, $id_trader, $prixTrade, $user;
    protected $listeners = ['negotiationEnded' => '$refresh'];

    public function mount($id)
    {

        // Récupérer la notification par son ID ou échouer
        $this->notification = DatabaseNotification::findOrFail($id);

        // Récupérer les données nécessaires depuis la notification
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->produit = ProduitService::find($this->idProd);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);

        // Assurez-vous que $this->achatdirect existe avant d'accéder à ses propriétés
        if (!$this->achatdirect) {
            throw new \Exception("Achat direct introuvable pour l'ID: " . $this->notification->data['achat_id']);
        }

        // Update the locations loading logic
        $this->loadLocations();

        // Déterminer la valeur de $Valuecode_unique
        switch ($this->achatdirect->type_achat) {
            case 'appelOffreGrouper':
            case 'appelOffre':
            case 'OffreGrouper':
            case 'achatDirect':
                $this->Valuecode_unique = $this->notification->data['code_unique'] ?? null;
                break;
            default:
                $this->Valuecode_unique = $this->achatdirect->code_unique;
        }

        $countdown = Countdown::where('code_unique', $this->Valuecode_unique)
            ->where('is_active', false)
            ->first();

        if ($countdown && !$this->achatdirect->count) {
            $this->achatdirect->update(['count' => true]);
        }
        // Écouter les messages en temps réel (Livewire/AlpineJS ou autre)
        $this->listenForMessage();
    }

    private function loadLocations()
    {
        switch ($this->achatdirect->type_achat) {
            case 'OffreGrouper':
                $this->usersLocations = userquantites::where('code_unique', $this->achatdirect->code_unique)
                    ->select('user_id', 'localite')
                    ->distinct()
                    ->get();
                break;
            default:
                $this->usersLocations = collect([$this->achatdirect->userTraderI])
                    ->map(function ($user) {
                        return (object)[
                            'localite' => $user->commune
                        ];
                    });
                break;
        }
    }

    #[On('echo:comments.{Valuecode_unique},CommentSubmitted')]
    public function listenForMessage()
    {
        // Récupérer les commentaires
        $this->comments = Comment::with('user')
            ->where('code_unique', $this->Valuecode_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'asc')
            ->get();

        // Prix le plus bas
        $this->prixLePlusBas = Comment::where('code_unique', $this->Valuecode_unique)
            ->whereNotNull('prixTrade')
            ->min('prixTrade');

        // Offre initiale (la plus ancienne)
        $this->offreIniatiale = Comment::where('code_unique', $this->Valuecode_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('created_at', 'asc')
            ->first();

        $this->isNegociationActive = !$this->achatdirect->count;

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

        DB::beginTransaction();
        try {
            // Récupérer d'abord l'offre initiale pour la validation
            $offreInitiale = Comment::where('code_unique', $this->Valuecode_unique)
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

            // Créer un commentaire
            $comment = Comment::create([
                'prixTrade' => $validatedData['prixTrade'],
                'code_unique' => $this->Valuecode_unique,
                'id_trader' => Auth::id(),
                'quantiteC' => $this->achatdirect->quantité,
                'id_prod' => $this->achatdirect->idProd,
                'prixProd' => $this->produit->prix,
                'id_sender' => json_encode($this->achatdirect->userTrader),
            ]);


            event(new CommentSubmitted($this->Valuecode_unique, $comment));
            $this->listenForMessage();

            // Réinitialiser le champ du formulaire
            $this->reset(['prixTrade']);

            //Committer la transaction
            DB::commit();
        } catch (\Exception $e) {
            // Annuler la transaction en cas d'erreur
            DB::rollBack();
            session()->flash('error', 'Erreur lors de l\'ajout du commentaire: ' . $e->getMessage());
        }
    }


    public function render()
    {
        return view('livewire.livraison-achatdirect');
    }
}

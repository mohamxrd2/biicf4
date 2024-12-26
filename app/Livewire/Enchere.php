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
    protected $isNegociationActive;



    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);

        $this->id_trader = Auth::user()->id ?? null;
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->produit = ProduitService::find($this->idProd);
        $this->offgroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->first();



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


    public function commentoffgroup()
    {
        // Vérifier si la négociation est terminée
        if ($this->offgroupe->count) {
            $this->dispatch(
                'formSubmitted',
                'La négociation est terminée. Vous ne pouvez plus soumettre d\'offres.'
            );
            return;
        }

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

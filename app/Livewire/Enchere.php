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
    public $produit, $nombreParticipants, $offgroupe;
    public $time;
    public $error;
    public $lastActivity;
    public $prixLePlusBas;
    public $offreIniatiale;
    public $isNegociationActive;
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

            $countdown = Countdown::where('code_unique', $this->offgroupe->code_unique)
                ->where('is_active', false)
                ->first();
            if ($countdown && !$this->offgroupe->count) {
                $this->offgroupe->update(['count' => true]);
            }

            $this->listenForMessage();
        } catch (Exception $e) {
            Log::error('Erreur dans le montage de l\'enchère', [
                'error' => $e->getMessage(),
                'notification_id' => $id
            ]);
            throw $e;
        }
    }

    #[On('echo:comments,CommentSubmitted')]
    public function listenForMessage()
    {
        $code_unique = $this->notification->data['code_unique'];

        $this->comments = Comment::with('user')
            ->where('code_unique', $code_unique)
            ->whereNotNull('prixTrade')
            ->orderBy('prixTrade', 'desc')
            ->get();

        $this->prixLePlusBas = Comment::where('code_unique', $code_unique)
            ->whereNotNull('prixTrade')
            ->max('prixTrade');

        $this->offreIniatiale =  $this->produit->prix;


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


    public function commentoffgroup()
    {
        if ($this->offgroupe->count) {
            $this->dispatch(
                'formSubmitted',
                'La négociation est terminée. Vous ne pouvez plus soumettre d\'offres.'
            );
            return;
        }

        $code_unique = $this->notification->data['code_unique'];

        try {
            DB::beginTransaction();

            $offreInitiale = $this->produit->prix;

            $validatedData = $this->validate([
                'prixTrade' => [
                    'required',
                    'numeric',
                    function ($attribute, $value, $fail) use ($offreInitiale) {

                        if ($offreInitiale && $value < $offreInitiale) {
                            $fail("Le prix doit être supérieur à {$offreInitiale}");
                        }
                    }
                ]
            ]);

            $comment = Comment::create([
                'prixTrade' => $validatedData['prixTrade'],
                'code_unique' => $code_unique,
                'id_trader' => Auth::id(),
                'id_prod' => $this->produit->id,
            ]);

            broadcast(new CommentSubmitted($validatedData['prixTrade'], $comment->id))->toOthers();

            DB::commit();
            $this->reset('prixTrade');
            // Optionnel: Ajouter une notification ou un message de succès
            session()->flash('message', 'Commentaire sur le taux ajouté avec succès.');
            $this->listenForMessage();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'enchère', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
            session()->flash('error', 'Erreur lors de l\'enchère: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.enchere');
    }
}

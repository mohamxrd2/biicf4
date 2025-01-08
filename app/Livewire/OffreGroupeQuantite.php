<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Models\OffreGroupe;
use Exception;
use App\Services\RecuperationTimer;

use App\Models\userquantites;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class OffreGroupeQuantite extends Component
{
    public $id;
    public $notification;
    public $produit;
    public $localite;
    public $selectedOption;
    public $OffreGroupe;
    public $participants = 0;
    public $existingQuantite;
    public $premierFournisseur;
    public $quantiteTotale = 0;
    public $quantite;
    public $oldestComment;
    public $oldestCommentDate;
    public $isOpen;
    public $groupages = []; // Liste des groupages existants

    public $time;
    public $error;
    protected $listeners = ['negotiationEnded' => '$refresh'];

    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function mount($id)
    {
        try {
            // Récupération de la notification
            $this->notification = DatabaseNotification::findOrFail($id);

            $this->fetchOffreGroupe();
            $this->fetchProduit();
            $this->initializeGroupageData();
        } catch (Exception $e) {
            Log::error('Erreur dans mount', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du chargement des données.');
        }
    }

    private function fetchOffreGroupe()
    {
        $codeUnique = $this->notification->data['code_unique'];
        $this->OffreGroupe = OffreGroupe::where('code_unique', $codeUnique)->first();

        $countdown = Countdown::where('code_unique', $codeUnique)
            ->where('is_active', false)
            ->first();
        if ($countdown && !$this->OffreGroupe->count) {
            $this->OffreGroupe->update(['count' => true]);
        }
    }

    private function fetchProduit()
    {
        $this->produit = ProduitService::find($this->notification->data['idProd']);

        if (!$this->produit) {
            abort(404, 'Produit non trouvé.');
        }
    }

    private function initializeGroupageData()
    {
        $codeUnique = $this->notification->data['code_unique'];

        $this->reloadGroupages($codeUnique);
    }

    private function reloadGroupages($codeUnique)
    {
        $this->groupages = userquantites::with('user')
            ->where('code_unique', $codeUnique)
            ->orderBy('created_at', 'asc')
            ->get();

        $this->participants = userquantites::where('code_unique', $codeUnique)
            ->distinct('user_id')
            ->count('user_id');

        $this->quantiteTotale = userquantites::where('code_unique', $codeUnique)
            ->sum('quantite');

        $this->existingQuantite = userquantites::where('code_unique', $codeUnique)
            ->where('user_id', Auth::id())
            ->first();
    }



    #[On('echo:quantite-channel,AjoutQuantiteOffre')]
    public function actualiserDonnees($event)
    {
        if (!$event['codeUnique']) {
            Log::error('Code unique non fourni lors de la mise à jour.');
            return;
        }

        $this->quantiteTotale += $event['quantite'];

        $this->reloadGroupages($event['codeUnique']);
        $this->dispatch('formSubmitted', "Nouvelle quantité de {$event['quantite']} ajoutée par " . Auth::user()->name . " avec succès !");
    }

    public function storeoffre()
    {
        try {
            // Validation des données d'entrée
            $validatedData = $this->validate([
                'quantite' => 'required|integer|min:1|max:' . $this->OffreGroupe->quantite,
                'localite' => 'nullable|string',
            ]);

            $user = Auth::user();
            $codeUnique = $this->notification->data['code_unique'];

            // Vérifier si une quantité existe déjà pour ce code unique
            $existingQuantite = userquantites::where('code_unique', $codeUnique)
                ->where('user_id', $user->id)
                ->first();
            // Calculer la nouvelle quantité totale
            $nouvelleQuantiteTotale = $this->quantiteTotale + $validatedData['quantite'];

            // Vérifier si la nouvelle quantité totale dépasse la limite
            if ($nouvelleQuantiteTotale > $this->OffreGroupe->quantite) {
                $quantiteRestante = $this->OffreGroupe->quantite - $this->quantiteTotale;
                session()->flash('error', "La quantité demandée dépasse la limite disponible. Il reste seulement {$quantiteRestante} unité(s).");
                return;
            }

            if ($existingQuantite) {
                // Mise à jour de la quantité existante
                $existingQuantite->quantite += $validatedData['quantite'];
                $existingQuantite->save();
            } else {
                // Création d'un nouvel enregistrement
                userquantites::create([
                    'code_unique' => $codeUnique,
                    'user_id' => $user->id,
                    'localite' => $validatedData['localite'],
                    'quantite' => $validatedData['quantite'],
                ]);
            }

            // Mise à jour des données locales
            $this->quantiteTotale = $nouvelleQuantiteTotale;

            $this->reloadGroupages($codeUnique);

            // Diffusion de l'événement Laravel
            broadcast(new AjoutQuantiteOffre($validatedData['quantite'], $codeUnique));

            $this->reset('quantite', 'localite');

            $this->isOpen = false;
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'ajout de la quantité.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de l\'ajout de la quantité.');
        }
    }

    public function render()
    {
        return view('livewire.offre-groupe-quantite');
    }
}

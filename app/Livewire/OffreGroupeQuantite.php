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

            // Récupérer l'heure du serveur
            $this->time = $this->recuperationTimer->getTime();
            $this->error = $this->recuperationTimer->error;

            $this->fetchOffreGroupe();
            $this->fetchProduit();
            $this->initializeGroupageData();
        } catch (Exception $e) {
            Log::error('Erreur dans mount', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du chargement des données.');
            return redirect()->route('home'); // Redirection si une erreur critique survient
        }
    }

    private function fetchOffreGroupe()
    {
        $codeUnique = $this->notification->data['code_unique'];
        $this->OffreGroupe = OffreGroupe::where('code_unique', $codeUnique)->first();
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

        // Récupérer le commentaire le plus ancien avec code_unique et start_time non nul
        $this->oldestComment = Countdown::where('code_unique', $codeUnique)
            ->whereNotNull('start_time')
            ->where('notified', false)
            ->orderBy('created_at', 'asc')
            ->first();

        // Assurez-vous que la date est en format ISO 8601 pour JavaScript
        $this->oldestCommentDate = $this->oldestComment
            ? Carbon::parse($this->oldestComment->start_time)->toIso8601String()
            : null;


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

    // Ajouter un écouteur d'événements dans Livewire
    protected $listeners = [
        'compteReboursFini', // Cet événement appellera une méthode `compteReboursFini`
    ];
    public function compteReboursFini()
    {
        try {
            // Vérifier que le groupe d'appel d'offre est valide
            if (!$this->OffreGroupe) {
                throw new Exception('Groupe d\'appel d\'offres introuvable.');
            }
            // Mettre à jour l'attribut 'finish'
            $this->OffreGroupe->update(['count' => true]);

            // Émettre un événement Livewire pour notifier la fin
            $this->dispatch('formSubmitted', 'Temps écoulé, groupage terminé.');
        } catch (Exception $e) {
            Log::error('Erreur lors de la fin du compte à rebours.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la fin du compte à rebours.');
        }
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
                'quantite' => 'required|integer|min:1',
                'localite' => 'nullable|string',
            ]);

            $user = Auth::user();
            $codeUnique = $this->notification->data['code_unique'];

            // Vérifier si une quantité existe déjà pour ce code unique
            $existingQuantite = userquantites::where('code_unique', $codeUnique)
                ->where('user_id', $user->id)
                ->first();

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
            $this->quantiteTotale += $validatedData['quantite'];

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

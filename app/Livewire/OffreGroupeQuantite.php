<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\Countdown;
use App\Models\OffreGroupe;
use App\Models\ProduitService;
use App\Models\userquantites;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

class OffreGroupeQuantite extends Component
{
    public $id;
    public $notification;
    public $produit;
    public $localite;
    public $selectedOption;
    public $appelOffreGroup;
    public $participants = 0;
    public $premierFournisseur;
    public $quantiteTotale = 0;
    public $quantite;
    public $oldestComment;
    public $oldestCommentDate;
    public $groupages = []; // Liste des groupages existants

    public function mount($id)
    {
        // Récupération de la notification
        $this->notification = DatabaseNotification::findOrFail($id);

        // Vérifier que le produit existe
        $this->produit = ProduitService::find($this->notification->data['idProd']);
        if (!$this->produit) {
            abort(404, 'Produit non trouvé.');
        }

        // Récupérer le commentaire le plus ancien
        $this->oldestComment = Countdown::where('code_unique', $this->notification->data['code_unique'])
            ->whereNotNull('start_time')
            ->orderBy('created_at', 'asc')
            ->first();

        // Compter les participants distincts
        $this->participants = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->distinct('user_id') // Filtrer les doublons
            ->count('user_id');

        // Somme des quantités
        $this->quantiteTotale = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->sum('quantite');

        // Participant le plus ancien
        $this->premierFournisseur = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->orderBy('created_at', 'asc')
            ->first();

        // Charger les groupages
        $this->groupages = userquantites::with('user')
            ->where('code_unique', $this->notification->data['code_unique'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Format de la date du commentaire le plus ancien
        $this->oldestCommentDate = $this->oldestComment
            ? $this->oldestComment->created_at->toIso8601String()
            : null;
    }


    public function storeoffre()
    {
        try {

            $validatedData = $this->validate([
                'quantite' => 'required|integer|min:1',
            ]);

            $quantite = userquantites::create([
                'code_unique' => $this->notification->data['code_unique'],
                'user_id' => Auth::id(),
                'quantite' => $validatedData['quantite'],
                // Exemple à remplacer si nécessaire
            ]);

            // Mise à jour des données locales
            $this->quantiteTotale += $validatedData['quantite'];

            // Ajouter aux groupages (rechargement de la collection)
            $this->groupages = userquantites::with('user')
                ->where('code_unique', $this->notification->data['code_unique'])
                ->orderBy('created_at', 'asc')
                ->get();
                
            // Réinitialiser le champ de quantité
            $this->reset('quantite');

            // Message de succès
            session()->flash('success', 'Quantité ajoutée avec succès');
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

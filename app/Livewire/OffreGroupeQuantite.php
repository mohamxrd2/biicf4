<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\Countdown;
use App\Models\ProduitService;
use App\Models\OffreGroupe;
use Exception;
use App\Models\userquantites;

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
    public $existingQuantite;
    public $premierFournisseur;
    public $quantiteTotale = 0;
    public $quantite;
    public $oldestComment;
    public $oldestCommentDate;
    public $groupages = []; // Liste des groupages existants

    public function mount($id)
    {
        try {
            // Récupération de la notification
            $this->notification = DatabaseNotification::findOrFail($id);

            // Récupérer le produit lié à la notification
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
                ->distinct('user_id')
                ->count('user_id');

            // Somme des quantités
            $this->quantiteTotale = userquantites::where('code_unique', $this->notification->data['code_unique'])
                ->sum('quantite');

            // Participant le plus ancien
            $this->premierFournisseur = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])
                ->orderBy('created_at', 'asc')
                ->first();

            // Charger les groupages
            $this->groupages = userquantites::with('user')
                ->where('code_unique', $this->notification->data['code_unique'])
                ->orderBy('created_at', 'asc')
                ->get();

            // Vérifier si l'utilisateur a déjà soumis une quantité
            $this->existingQuantite = userquantites::where('code_unique', $this->notification->data['code_unique'])
                ->where('user_id', Auth::id())
                ->first();

            // Format de la date du commentaire le plus ancien
            $this->oldestCommentDate = $this->oldestComment
                ? $this->oldestComment->created_at->toIso8601String()
                : null;
        } catch (Exception $e) {
            Log::error('Erreur dans mount', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors du chargement des données.');
            return redirect()->route('home'); // Redirection si une erreur critique survient
        }
    }

    protected $listeners = ['compteReboursFini'];

    public function compteReboursFini()
    {
        try {
            // Vérifier que le groupe d'appel d'offre est valide
            if (!$this->appelOffreGroup) {
                throw new Exception('Groupe d\'appel d\'offres introuvable.');
            }

            // Mettre à jour l'attribut 'finish'
            $this->appelOffreGroup->update(['count' => true]);

            // Émettre un événement Livewire pour notifier la fin
            $this->dispatch('formSubmitted', 'Temps écoulé, groupage terminé.');
        } catch (Exception $e) {
            Log::error('Erreur lors de la fin du compte à rebours.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Erreur lors de la fin du compte à rebours.');
        }
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

            // Rechargement des groupages
            $this->groupages = OffreGroupe::with('user')
                ->where('code_unique', $codeUnique)
                ->orderBy('created_at', 'asc')
                ->get();

            // Mise à jour des participants distincts
            $this->participants = OffreGroupe::where('code_unique', $codeUnique)
                ->distinct('user_id')
                ->count('user_id');

            // Réinitialisation des champs du formulaire
            $this->reset('quantite');

            // Message de succès
            session()->flash('success', 'Quantité ajoutée avec succès.');
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

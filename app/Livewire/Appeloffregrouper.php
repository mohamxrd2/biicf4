<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\AppelOffreGrouper as ModelsAppelOffreGrouper;
use App\Models\gelement;
use App\Models\userquantites;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class Appeloffregrouper extends Component
{
    public $notification;
    public $id;
    public $appelOffreGroup;
    public $datePlusAncienne;
    public $sumquantite;
    public $appelOffreGroupcount;
    public $quantite;
    public $localite;
    public $selectedOption;
    public $groupages;
    public $existingQuantite;
    public $modalOpen;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $Idoffre = $this->notification->data['offre_id'] ?? null;

        // Attempt to retrieve the grouped offer by its ID
        $this->appelOffreGroup = ModelsAppelOffreGrouper::find($Idoffre);
        $codesUniques = $this->appelOffreGroup->codeunique;
        $this->datePlusAncienne = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');


        $this->sumquantite = userquantites::where('code_unique', $codesUniques)->sum('quantite');
        $this->appelOffreGroupcount = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)
            ->distinct('user_id') // Prend uniquement les valeurs uniques de user_id
            ->count('user_id');   // Compte les valeurs distinctes

        // Charger les groupages
        $this->groupages = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->orderBy('created_at', 'asc')
            ->get();

        // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
        $this->existingQuantite = userquantites::where('code_unique', $this->appelOffreGroup->codeunique)
            ->where('user_id', Auth::id())
            ->first();
    }

    protected $listeners = ['compteReboursFini'];

    public function compteReboursFini()
    {
        // Mettre à jour l'attribut 'finish' du demandeCredit
        $this->appelOffreGroup->update([
            'count' => true,
            $this->dispatch(
                'formSubmitted',
                'Temps écoule, Groupage terminé.'
            )
        ]);
    }


    public function storeOffre()
    {
        DB::beginTransaction(); // Démarrer une transaction

        try {
            // Valider les données du formulaire
            $validatedData = $this->validate([
                'quantite' => 'required|integer',
                'localite' => 'nullable|string',
            ]);

            // Récupérer l'utilisateur actuel et l'appel d'offre en cours
            $user = Auth::user();
            $appelOffreGroup = $this->appelOffreGroup;

            // Vérifier si le groupe d'appel d'offre existe
            if (!$appelOffreGroup) {
                session()->flash('error', 'Appel d\'offre introuvable.');
                return;
            }

            $userWallet = Wallet::where('user_id', $user)->first();

            if (!$userWallet) {
                Log::error('Portefeuille introuvable.', ['userId' => $user]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            // Calcul du coût total
            $prixUnitaire = $appelOffreGroup->lowestPricedProduct ?? 0;
            $quantite = $validatedData['quantite'];
            $montantTotal = $prixUnitaire * $quantite;

            // Vérifier les fonds disponibles
            if ($user->balance < $montantTotal) {
                session()->flash('error', 'Fonds insuffisants pour soumettre cette quantité.');
                return;
            }

            // Décrémente le solde utilisateur
            $userWallet = $user->wallet; // Assurez-vous que $user->wallet retourne correctement le portefeuille
            $userWallet->decrement('balance', $montantTotal);

            // Enregistrement dans la table `gelement`
            gelement::create([
                'id_wallet' => $userWallet->id,
                'amount' => $montantTotal,
                'reference_id' => $appelOffreGroup->codeunique,
            ]);

            // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
            $existingQuantite = userquantites::where('code_unique', $appelOffreGroup->codeunique)
                ->where('user_id', $user->id)
                ->first();

            if ($existingQuantite) {
                // Mise à jour de la quantité existante
                $existingQuantite->quantite += $quantite;
                $existingQuantite->save();
            } else {
                // Création d'un nouvel enregistrement
                userquantites::create([
                    'code_unique' => $appelOffreGroup->codeunique,
                    'user_id' => $user->id,
                    'localite' => $validatedData['localite'],
                    'quantite' => $quantite,
                ]);
            }

            // Mise à jour des données pour le composant Livewire
            $this->groupages = userquantites::where('code_unique', $appelOffreGroup->codeunique)
                ->orderBy('created_at', 'asc')
                ->get();
            $this->sumquantite = ModelsAppelOffreGrouper::where('codeunique', $appelOffreGroup->codeunique)
                ->sum('quantity');
            $this->appelOffreGroupcount = ModelsAppelOffreGrouper::where('codeunique', $appelOffreGroup->codeunique)
                ->distinct('user_id')
                ->count('user_id');

            // Réinitialiser les champs du formulaire
            $this->reset('quantite', 'localite');

            // Fermer le modal
            $this->modalOpen = false;

            // Commit de la transaction
            DB::commit();

            // Message de succès
            session()->flash('success', 'Quantité ajoutée ou mise à jour avec succès');
        } catch (Exception $e) {
            // Annuler les changements
            DB::rollBack();

            // Log l'erreur et afficher un message d'erreur
            Log::error('Erreur lors de l\'ajout ou mise à jour : ' . $e->getMessage());
            session()->flash('error', 'Erreur lors de l\'ajout ou mise à jour de la quantité.');
        }
    }



    public function render()
    {
        return view('livewire.appeloffregrouper');
    }
}

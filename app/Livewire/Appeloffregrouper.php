<?php

namespace App\Livewire;

use App\Events\AjoutQuantiteOffre;
use App\Models\AppelOffreGrouper as ModelsAppelOffreGrouper;
use App\Models\userquantites;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
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


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $Idoffre = $this->notification->data['offre_id'] ?? null;

        // Attempt to retrieve the grouped offer by its ID
        $this->appelOffreGroup = ModelsAppelOffreGrouper::find($Idoffre);
        $codesUniques = $this->appelOffreGroup->codeunique;
        $this->datePlusAncienne = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');


        $this->sumquantite = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');
        $this->appelOffreGroupcount = ModelsAppelOffreGrouper::where('codeunique', $codesUniques)
            ->distinct('user_id') // Prend uniquement les valeurs uniques de user_id
            ->count('user_id');   // Compte les valeurs distinctes

    }






    public function storeoffre()
    {
        try {
            // Valider les données du formulaire
            Log::info('Début de la validation des données du formulaire.');
            $validatedData = $this->validate([
                'quantite' => 'required|integer',
                'localite' => 'required|string',
            ]);

            // Créer un nouvel enregistrement dans la table offregroupe
            Log::info('Création d\'un nouvel enregistrement dans AppelOffreGrouper.');
            $offregroupe = new ModelsAppelOffreGrouper();
            $offregroupe->codeunique = $this->appelOffreGroup->codeunique;
            $offregroupe->user_id = Auth::id();
            $offregroupe->quantity = $validatedData['quantite'];
            $offregroupe->save();



            // Ajouter dans la table userquantites
            Log::info('Ajout d\'une nouvelle quantité dans userquantites.');
            $quantite = new userquantites();
            $quantite->code_unique = $this->appelOffreGroup->codeunique; // Vous devez définir `codeUnique` correctement
            $quantite->user_id = Auth::id(); // Vous devez définir `userId` correctement
            $quantite->localite = $validatedData['localite']; // Vous devez définir `userId` correctement
            $quantite->quantite = $validatedData['quantite'];
            $quantite->save();

            Log::info('Enregistrement dans userquantites sauvegardé.', ['quantite_id' => $quantite->id]);
            $this->reset('quantite', 'localite', 'selectedOption');
            // Flash success message
            session()->flash('success', 'Quantité ajoutée avec succès');
            Log::info('Message de succès flashé.', ['message' => 'Quantité ajoutée avec succès']);
        } catch (Exception $e) {
            // Log l'erreur
            Log::error('Erreur lors de l\'enregistrement des données.', ['error' => $e->getMessage()]);
            // Vous pouvez également définir un message d'erreur pour la session si nécessaire
            session()->flash('error', 'Erreur lors de l\'ajout de la quantité.');
        }
    }
    public function render()
    {
        return view('livewire.appeloffregrouper');
    }
}

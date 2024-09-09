<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\AppelOffreGrouper;
use App\Models\UserQuantites;

class AjoutdeQuantite extends Component
{
    public $codeUnique;
    public $userId;
    public $quantite;

    public function mount()
    {
        // Récupérer l'ID de l'utilisateur connecté
        $this->userId = Auth::id();
    }

    public function storeoffre(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'codeUnique' => 'required|string',
            'quantite' => 'required|integer'
        ]);

        // Récupérer l'offre groupée par son ID
        $appelOffreGroup = AppelOffreGrouper::find($validatedData['codeUnique']);

        // Créer un nouvel enregistrement dans la table offregroupe
        $offreGroupe = new AppelOffreGrouper();
        $offreGroupe->codeunique = $validatedData['codeUnique'];
        $offreGroupe->user_id = $this->userId;
        $offreGroupe->quantity = $validatedData['quantite'];
        $offreGroupe->save();

        // Ajout dans la table userquantites
        $quantite = new UserQuantites();
        $quantite->code_unique = $validatedData['codeUnique'];
        $quantite->user_id = $this->userId;
        $quantite->quantite = $validatedData['quantite'];
        $quantite->save();
    }

    public function render()
    {
        return view('livewire.ajoutde-quantite');
    }
}

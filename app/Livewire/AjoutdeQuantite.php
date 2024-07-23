<?php

namespace App\Livewire;

use Livewire\Component;

class AjoutdeQuantite extends Component
{
    // Récupérer l'ID de l'utilisateur connecté
    $userId = Auth::guard('web')->id();
    // Récupérer l'offre groupée par son ID
    $appelOffreGroup = AppelOffreGrouper::find($id);
    public function storeoffre(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'codeUnique' => 'required|string',
            'userId' => 'required|integer',
            'quantite' => 'required|integer'
        ]);



        // Créer un nouvel enregistrement dans la table offregroupe
        $offregroupe = new AppelOffreGrouper();
        $offregroupe->codeunique = $validatedData['codeUnique'];
        $offregroupe->user_id = $validatedData['userId'];
        $offregroupe->quantity = $validatedData['quantite'];
        $offregroupe->save();

        //ajout dans table userquantites
        $quantite = new userquantites();
        $quantite->code_unique = $validatedData['codeUnique'];
        $quantite->user_id = $validatedData['userId'];
        $quantite->quantite = $validatedData['quantite'];
        $quantite->save();
    public function render()
    {
        return view('livewire.ajoutde-quantite');
    }
}

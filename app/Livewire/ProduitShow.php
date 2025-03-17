<?php

namespace App\Livewire;

use App\Models\ProduitService;
use Livewire\Component;

class ProduitShow extends Component
{
    public $id;
    public $produit;

    public function mount($id)
    {
        $this->id = $id;

        // Charger le produit basé sur l'ID fourni
        $this->produit = ProduitService::findOrFail($id);
    }

    public function accepter()
    {
        // Mettre à jour le statut du produit
        $this->produit->update(['statuts' => 'Accepté']);

        // Rafraîchir les données
        $this->produit = ProduitService::findOrFail($this->id);

        // Ajouter un message de confirmation
        session()->flash('success', 'Publication acceptée avec succès');
    }

    public function refuser()
    {
        // Mettre à jour le statut du produit
        $this->produit->update(['statuts' => 'Refusé']);

        // Rafraîchir les données
        $this->produit = ProduitService::findOrFail($this->id);

        // Ajouter un message de confirmation
        session()->flash('success', 'Publication refusée avec succès');
    }

    public function render()
    {
        // On transmet directement $this->produit à la vue avec le nom de variable 'produits'
        // pour correspondre à ce qui est utilisé dans le template
        return view('livewire.produit-show', ['produits' => $this->produit]);
    }
}

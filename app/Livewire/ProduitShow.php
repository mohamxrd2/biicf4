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

        // Charger la produit basée sur l'ID fourni
        $this->produit = ProduitService::findOrFail($id);
    }
    public function accepter()
    {
        // Mettre à jour le statut de la produit
        $this->produit->update(['statuts' => 'Accepté']);

        // Rafraîchir les données
        $this->produit = ProduitService::findOrFail($this->id);
    }
    public function refuser()
    {
        // Mettre à jour le statut de la produit
        $this->produit->update(['statuts' => 'Refusé']);

        // Rafraîchir les données
        $this->produit = ProduitService::findOrFail($this->id);
    }
    public function render()
    {
        $produits = ProduitService::find($this->id);

        return view('livewire.produit-show', compact('produits'));
    }
}

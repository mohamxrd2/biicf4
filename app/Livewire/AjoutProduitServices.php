<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

class AjoutProduitServices extends Component
{

    use WithFileUploads;

    public $type  = '';
    public $name  = '';
    public $conditionnement  = '';
    public $format  = '';
    public $qteProd_min  = '';
    public $qteProd_max  = '';
    public $prix  = '';
    public $livraison  = '';
    public $qualification  = '';
    public $specialite  = '';
    public $qte_service  = '';
    public $ville  = '';
    public $commune  = '';

    public $description  = '';


    public function submit()
    {
        dd($this->validate([
            'type' => 'required|string|in:produits,services',
            'name' => 'required|string|max:255',
            'conditionnement' => 'required_if:type,produits|string|max:255',
            'format' => 'required_if:type,produits|string',
            'qteProd_min' => 'required_if:type,produits|string',
            'qteProd_max' => 'required_if:type,produits|string',
            'prix' => 'required|integer',
            'livraison' => 'required_if:type,produits|string|in:oui,non',
            'qualification' => 'required_if:type,services|string',
            'specialite' => 'required_if:type,services|string',
            'qte_service' => 'required_if:type,services|string',
            'ville' => 'required|string',
            'commune' => 'required|string',
            'images' => 'image|mimes:jpeg,png|max:2048', // Adjust validation rules as needed
            'description' => 'required|string',
        ]));

        try {

            ProduitService::create([
                'type' => $this->type,
                'name' => $this->name, // Adjusted for 'produits'
                'conditionnement' => $this->type === 'produits' ? $this->conditionnement : null,
                'format' => $this->type === 'produits' ? $this->format : null,
                'qteProd_min' => $this->type === 'produits' ? $this->qteProd_min : null,
                'qteProd_max' => $this->type === 'produits' ? $this->qteProd_max : null,
                'prix' => $this->prix,
                'livraison' => $this->type === 'produits' ? $this->livraison : null,
                'qualification' => $this->type === 'services' ? $this->qualification : null,
                'specialite' => $this->type === 'services' ? $this->specialite : null,
                'qte_service' => $this->type === 'services' ? $this->qte_service : null,
                'ville' => $this->ville,
                'commune' => $this->commune,
                'desrip' => $this->description,
                'user_id' => auth()->id(),
                'images' => json_encode($imagePaths), // Stockage des chemins des images
            ]);





            session()->flash('message', 'Produit ou service ajouté avec succès!');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de l\'enregistrement.');
        }
    }
    public function render()
    {
        return view('livewire.ajout-produit-services');
    }
}

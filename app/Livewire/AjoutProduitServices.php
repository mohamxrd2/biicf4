<?php

namespace App\Livewire;

use App\Models\CategorieProduits_Servives;
use App\Models\ProduitService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;


class AjoutProduitServices extends Component
{

    use WithFileUploads;

    public $type  = '';
    public $generateReference = false; // Ajoutez cette propriété pour l'état de la case à cocher
    public $reference = '';
    public $name  = '';
    //  produit
    public $conditionnement  = '';
    public $format  = '';
    public $particularite  = '';
    public $origine  = '';
    public $qteProd_min  = '';
    public $qteProd_max  = '';
    public $specification  = '';
    //
    public $prix  = '';
    //Service
    public $qualification  = '';
    public $specialite  = '';
    public $qte_service  = '';
    public $depart  = '';
    public $ville  = '';
    public $commune  = '';
    //
    public $categories  = '';


    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
    }
    // Méthode appelée lors du clic sur la case à cocher
    public function toggleGenerateReference()
    {
        $this->generateReference = !$this->generateReference; // Inverse l'état de la case à cocher

        if ($this->generateReference) {
            $this->reference = $this->generateUniqueReference();
        } else {
            $this->reference = ''; // Efface la référence si la case est décochée
        }
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
    public function submit()
    {
        $this->validate([
            'type' => 'required|string|in:Produit,Service',
            'reference' => 'required|string',
            'name' => 'required|string|max:255',
            //produits
            'conditionnement' => 'required_if:type,Produit|string|max:255',
            'format' => 'required_if:type,Produit|string',
            'particularite' => 'required_if:type,Produit|string',
            'origine' => 'required_if:type,Produit|string',
            'qteProd_min' => 'required_if:type,Produit|integer',
            'qteProd_max' => 'required_if:type,Produit|integer',
            'specification' => 'required_if:type,Produit|string',
            //
            'prix' => 'required|integer',
            //service
            'qualification' => 'required_if:type,Service|string',
            'specialite' => 'required_if:type,Service|string',
            'qte_service' => 'required_if:type,Service|string',
            //
            'depart' => 'required|string',
            'ville' => 'required|string',
            'commune' => 'required|string',
        ]);



        ProduitService::create([
            'type' => $this->type,
            'reference' => $this->reference,
            'name' => $this->name, // Adjusted for 'Produit'
            //produit
            'conditionnement' => $this->type === 'Produit' ? $this->conditionnement : null,
            'format' => $this->type === 'Produit' ? $this->format : null,
            'particularite' => $this->type === 'Produit' ? $this->particularite : null,
            'origine' => $this->type === 'Produit' ? $this->origine : null,
            'qteProd_min' => $this->type === 'Produit' ? $this->qteProd_min : null,
            'qteProd_max' => $this->type === 'Produit' ? $this->qteProd_max : null,
            'specification' => $this->type === 'Produit' ? $this->specification : null,
            //
            'prix' => $this->prix,
            //service
            'qualification' => $this->type === 'Service' ? $this->qualification : null,
            'specialite' => $this->type === 'Service' ? $this->specialite : null,
            'qte_service' => $this->type === 'Service' ? $this->qte_service : null,
            //
            'depart' => $this->depart,
            'ville' => $this->ville,
            'commune' => $this->commune,
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Produit ou service ajouté avec succès!');

        $this->resetForm(); // Réinitialise le formulaire après la soumission
    }
    // Méthode pour réinitialiser les champs du formulaire
    public function resetForm()
    {
        $this->type = '';
        $this->generateReference = false;
        $this->reference = '';
        $this->name = '';
        $this->conditionnement = '';
        $this->format = '';
        $this->particularite = '';
        $this->origine = '';
        $this->qteProd_min = '';
        $this->qteProd_max = '';
        $this->specification = '';
        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->qte_service = '';
        $this->depart = '';
        $this->ville = '';
        $this->commune = '';
        // Réinitialiser les catégories si nécessaire
        $this->categories = CategorieProduits_Servives::all();
    }
    public function render()
    {
        return view('livewire.ajout-produit-services');
    }
}

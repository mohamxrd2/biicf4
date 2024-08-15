<?php

namespace App\Livewire;

use App\Models\CategorieProduits_Servives;
use App\Models\Consommation;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;


class AjoutConsommations extends Component
{
    public $consommations = [];
    public $consommation  = '';
    public $type  = '';

    public $name  = '';
    //  produit
    public $conditionnement  = '';
    public $particularite  = '';
    public $Periodicite  = '';
    public $qteProd  = '';

    //
    public $prix  = '';
    //Service
    public $qualification  = '';
    public $specialite  = '';
    public $qte_service  = '';
    public $pays  = '';
    public $depart  = '';
    public $ville  = '';
    public $commune  = '';
    public $selectedContinent;
    public $continents = [
        'Afrique',
        'Amérique du Nord',
        'Amérique du Sud',
        'Antarctique',
        'Asie',
        'Europe',
        'Océanie'
    ];
    public $selectedSous_region;
    public $sousregions = [
        'Afrique du Nord',
        'Afrique de l\'Ouest',
        'Afrique Centrale',
        'Afrique de l\'Est',
        'Afrique Australe',
        'Amérique du Nord',
        'Amérique Centrale ',
        'Amérique du Sud  ',
        'Caraïbes',
        'Asie de l\'Est',
        'Asie du Sud',
        'Asie du Sud-Est',
        'Asie Centrale',
        'Asie de l\'Ouest ',
        'Europe de l\'Est',
        'Europe de l\'Ouest',
        'Europe du Nord',
        'Europe du Sud',
        'Australie et Nouvelle-Zélande',
        'Mélanésie ',
        'Polynésie ',
        'Micronésie '
    ];
    public $produits = [];
    public $selectedCategories = [];
    public $selectedProduits = [];


    public $locked = false; // Déverrouillé par défaut

    public function mount()
    {
        // Récupère toutes les catégories
        $this->consommations = Consommation::all();
        $this->produits = collect(); // Ensure it's an empty Collection

    }


    public function updateProducts(array $selectedCategories)
    {
        $this->selectedCategories = $selectedCategories;

        // Update products based on selected consommations
        if ($this->selectedCategories) {
            $this->produits = ProduitService::whereIn('categorie_id', $this->selectedCategories)->get();
        } else {
            $this->produits = collect(); // Reset if no consommations selected
        }
    }
    public function updateProductDetails($productId)
    {
        $selectedProduct = ProduitService::find($productId);

        if ($selectedProduct) {
            // Remplir les propriétés avec les détails du produit sélectionné
            $this->name = $selectedProduct->name;
            $this->conditionnement = $selectedProduct->condProd;
            $this->particularite = $selectedProduct->Particularite;
            $this->locked = true;

            $this->prix = '';
            $this->qualification = ''; // Reset qualifications for services
            $this->specialite = ''; // Reset specialties for services
            $this->qte_service = ''; // Reset service quantity

        } else {
            // Réinitialiser les propriétés si aucun produit n'est trouvé
            $this->resetProductFields();
        }
    }

    public function updatedName()
    {
        if (empty($this->name)) {
            $this->locked = false; // Déverrouille l'input si le champ est vide
        }
    }

    protected function resetProductFields()
    {
        $this->conditionnement = '';
        $this->particularite = '';


        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->qte_service = '';
    }


    public function submit()
    {
       $this->validate([
            'type' => 'required|string|in:Produit,Service',
            'name' => 'required|string|max:255',
            //produits
            'conditionnement' => 'required_if:type,Produit|string|max:255',
            'particularite' => 'required_if:type,Produit|string',
            'qteProd' => 'required_if:type,Produit|integer',
            'prix' => 'required|integer',
            //service
            'qualification' => 'required_if:type,Service|string',
            'specialite' => 'required_if:type,Service|string',
            'selectedSous_region' => 'required|string',
            'selectedContinent' => 'required|string',
            'depart' => 'required|string',
            'pays' => 'required|string',
            'ville' => 'required|string',
            'commune' => 'required|string',

        ], [
            'name.required' => 'Le nom est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
            'reference.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
        ]);



            Consommation::create([
                'type' => $this->type,
                'name' => $this->name, // Adjusted for 'Produit'
                //produit
                'conditionnement' => $this->type === 'Produit' ? $this->conditionnement : null,
                'format' => $this->type === 'Produit' ? $this->particularite : null,
                'qte' => $this->type === 'Produit' ? $this->qteProd: null,
                //
                'prix' => $this->prix,
                //service
                'qalif_serv' => $this->type === 'Service' ? $this->qualification : null,
                'specialité' => $this->type === 'Service' ? $this->specialite : null,
                //
                'continent' => $this->selectedContinent,
                'Sous-Region' => $this->selectedSous_region,
                'departe' => $this->depart,
                'villeCons' => $this->ville,
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

        $this->name = '';
        $this->conditionnement = '';
        $this->particularite = '';

        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->qte_service = '';
        $this->depart = '';
        $this->ville = '';
        $this->commune = '';
        // Réinitialiser les catégories si nécessaire
        $this->consommations = CategorieProduits_Servives::all();
    }

    public function render()
    {
        return view('livewire.ajout-consommations');
    }
}

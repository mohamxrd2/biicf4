<?php

namespace App\Livewire;

use App\Models\CategorieProduits_Servives;
use App\Models\Consommation;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;


class AjoutConsommations extends Component
{
    public $categories = [];
    public $categorie  = '';
    public $type  = '';
    public $generateReference = false; // Ajoutez cette propriété pour l'état de la case à cocher
    public $reference = '';
    public $name  = '';
    //  produit
    public $conditionnement  = '';
    public $format  = '';
    public $origine  = '';
    public $qteProd  = '';
    public $periodicite = '';
    public $specification  = '';

    //
    public $prix  = '';
    //Service
    public $qualification  = '';
    public $specialite  = '';
    public $descrip  = '';
    public $Quantite  = '';


    public $produits = [];
    public $searchTerm = ''; // Add this property to hold the search term

    public $selectedCategories = [];
    public $selectedProduits = [];


    public $locked = false; // Déverrouillé par défaut
    public $countries = [];

    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
        $this->produits = collect(); // Ensure it's an empty Collection
        $this->fetchCountries();

    }

    public function updatedSearchTerm()
    {
        $this->produits = ProduitService::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->get();
    }

    public function fetchCountries()
    {
        try {
            $response = Http::get('https://restcountries.com/v3.1/all');
            $this->countries = collect($response->json())->pluck('name.common')->toArray();
        } catch (\Exception $e) {
            // Handle the error (e.g., log it, show an error message)
            $this->countries = [];
        }
    }
    public function updateProducts(array $selectedCategories)
    {
        $this->selectedCategories = $selectedCategories;

        // Update products based on selected categories
        if ($this->selectedCategories) {
            $this->produits = ProduitService::whereIn('categorie_id', $this->selectedCategories)->get();
        } else {
            $this->produits = collect(); // Reset if no categories selected
        }
    }
    public function updateProductDetails($productId)
    {
        $selectedProduct = ProduitService::find($productId);

        if ($selectedProduct) {
            // Remplir les propriétés avec les détails du produit sélectionné
            $this->reference = $selectedProduct->reference;
            $this->type = $selectedProduct->type;
            $this->name = $selectedProduct->name;
            $this->conditionnement = $selectedProduct->condProd;
            $this->format = $selectedProduct->formatProd;
            $this->origine = $selectedProduct->origine;
            $this->specification = $selectedProduct->specification;


            $this->qualification = $selectedProduct->qalifServ;
            $this->specialite = $selectedProduct->sepServ;
            $this->descrip = $selectedProduct->description;

            $this->qteProd = '';
            $this->prix = '';

            $this->locked = true;
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
        $this->format = '';
        $this->origine = '';
        $this->qteProd = '';
        $this->specification = '';
        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->descrip = '';
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
        dd($this->validate([
            'categorie' => 'required|string',
            'type' => 'required|string|in:Produit,Service',
            'reference' => 'required|string|unique:produit_services,reference,NULL,id,user_id,' . auth()->id(),
            'name' => 'required|string|max:255',
            // produits
            'conditionnement' => $this->type == 'Produit' ? 'required|string|max:255' : 'nullable|string',
            'format' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'origine' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'qteProd' => $this->type == 'Produit' ? 'required|integer' : 'nullable|integer',
            'specification' => $this->type == 'Produit' ? 'required|string'  : 'nullable|string',
            'periodicite' => $this->type == 'Produit' ? 'required|string'  : 'nullable|string',
            //
            'prix' => 'required|integer',
            // service
            'qualification' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'Quantite' => $this->type == 'Service' ? 'required|integer' : 'nullable|integer',
            'descrip' => $this->type == 'Service' ? 'required|string' : 'nullable|string',

        ], [
            'name.required' => 'Le nom est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
            'reference.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
        ]));

        try {
            // Création de la catégorie si elle n'existe pas encore
            if ($this->categorie) {
                $categorie = CategorieProduits_Servives::firstOrCreate([
                    'categorie_produit_services' => $this->categorie,
                ]);
            }

            Consommation::create([
                'type' => $this->type,
                'reference' => $this->reference,
                'name' => $this->name, // Adjusted for 'Produit'
                //produit
                'conditionnement' => $this->type === 'Produit' ? $this->conditionnement : null,
                'format' => $this->type === 'Produit' ? $this->format : null,
                'qte' => $this->type === 'Produit' ? $this->qteProd : null,
                'origine' => $this->type === 'Produit' ? $this->origine : null,
                //
                'specialité' => $this->specification,
                'periodicite' => $this->periodicite,
                'prix' => $this->prix,
                //service
                'qualif_serv' => $this->type === 'Service' ? $this->qualification : null,
                //

                'id_user' => auth()->id(),
                'categorie_id' => $categorie->id ?? null,
            ]);



            session()->flash('message', 'Produit ou service ajouté avec succès!');

            $this->resetForm(); // Réinitialise le formulaire après la soumission

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du produit ou service: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout du produit ou service.');
        }
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
        $this->origine = '';
        $this->qteProd = '';
        $this->specification = '';
        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->descrip = '';

        // Réinitialiser les catégories si nécessaire
        $this->categories = CategorieProduits_Servives::all();
    }



    public function render()
    {
        return view('livewire.ajout-consommations');
    }
}

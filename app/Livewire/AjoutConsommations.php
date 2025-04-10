<?php

namespace App\Livewire;

use App\Models\CategorieProduits_Servives;
use App\Models\Consommation;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

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
    protected $layout = 'components.layouts.app';

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

    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
        $this->produits = collect(); // Ensure it's an empty Collection
    }

    public function updatedSearchTerm()
    {
        $this->produits = Consommation::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->get();
    }


    public function updateProducts(array $selectedCategories)
    {
        $this->selectedCategories = $selectedCategories;
        if (!empty($this->selectedCategories)) {
            // Récupérer les catégories sélectionnées
            $categories = CategorieProduits_Servives::whereIn('id', $this->selectedCategories)->get();
            $this->selectedProduits = [];
            $this->resetForm();

            if ($categories->isNotEmpty()) {
                // Stocker les noms des catégories sélectionnées
                $this->categorie = $categories->pluck('categorie_produit_services')->toArray();

                // Update products based on selected categories
                $this->produits = Consommation::whereIn('categorie_id', $this->selectedCategories)
                    ->orderBy('reference')
                    ->get()
                    ->unique('reference'); // Ensure only unique references are taken
            } else {
                $this->categorie = [];
                $this->produits = collect(); // Aucune catégorie trouvée
            }
        } else {
            $this->categorie = [];
            $this->produits = collect(); // Aucun filtre appliqué
        }
    }
    public function updateProductDetails($productId)
    {
        $selectedProduct = Consommation::find($productId);

        if ($selectedProduct) {
            // Remplir les propriétés avec les détails du produit sélectionné
            $this->categorie = $selectedProduct->categorie->categorie_produit_services;
            $this->reference = $selectedProduct->reference;
            $this->type = $selectedProduct->type;
            $this->name = $selectedProduct->name;
            $this->conditionnement = $selectedProduct->conditionnement;
            $this->format = $selectedProduct->format;
            $this->origine = $selectedProduct->origine;


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
            'categorie' => 'required',
            'type' => 'required|string|in:Produit,Service',
            'reference' => [
                'required',
                'string',
                Rule::unique('consommations', 'reference')->where(function ($query) {
                    return $query->where('id_user', auth()->id());
                }),
            ],
            'name' => 'required|string|max:255',
            // produits
            'conditionnement' => $this->type == 'Produit' ? 'required|string|max:255' : 'nullable|string',
            'format' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'origine' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'qteProd' => $this->type == 'Produit' ? 'required|integer' : 'nullable|integer',
            //
            'periodicite' => 'required|string',
            'prix' => 'required|integer',
            // service
            // 'periodicite' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'Quantite' => $this->type == 'Service' ? 'required|integer' : 'nullable|integer',
            'descrip' => $this->type == 'Service' ? 'required|string' : 'nullable|string',

        ], [
            'name.required' => 'Le nom est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
            'reference.unique' => 'Vous etes deja fournisseur de ce produit',
        ]);

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
                'periodicite' => $this->periodicite,
                'prix' => $this->prix,
                //service
                'qualif_serv' => $this->type === 'Service' ? $this->qualification : null,
                'description' => $this->type === 'Service' ? $this->descrip : null,
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

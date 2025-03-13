<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Intervention\Image\Facades\Image;
use App\Models\CategorieProduits_Servives;


class AjoutProduitServices extends Component
{

    use WithFileUploads;
    public $categories = [];
    public $categorie  = '';
    public $type  = '';
    public $generateReference = false; // Ajoutez cette propriété pour l'état de la case à cocher
    public $reference = '';
    public $name  = '';
    //  produit
    public $conditionnement  = '';
    public $format  = '';
    public $poids  = '';
    public $particularite  = '';
    public $origine  = '';
    public $qteProd_min  = '';
    public $qteProd_max  = '';
    public $specification  = '';

    //
    public $prix  = '';
    //Service
    public $disponibilite  = '';
    public $lieu_intervention  = '';
    public $Duree  = '';
    public $qualification  = '';
    public $specialite  = '';
    public $Quantite  = '';
    public $descrip  = '';

    //

    public $produits = [];
    public $searchTerm = ''; // Add this property to hold the search term

    public $selectedCategories = [];
    public $selectedProduits = [];
    //photo
    public $photoProd1;
    public $photoProd2;
    public $photoProd3;
    public $photoProd4;
    public $photo1;

    public $locked = false; // Déverrouillé par défaut
    public $countries = [];
    public $user;

    public $isSubmitting = false;

    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
        $this->produits = collect(); // Ensure it's an empty Collection
        $this->user = User::find(auth()->id());
    }
    public function updatedSearchTerm()
    {
        $this->produits = ProduitService::where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('name', 'like', '%' . $this->searchTerm . '%')
            ->get();
    }

    public function updateProducts(array $selectedCategories)
    {
        $this->selectedCategories = $selectedCategories;

        if (!empty($this->selectedCategories)) {
            // Récupérer les catégories sélectionnées
            $categories = CategorieProduits_Servives::whereIn('id', $this->selectedCategories)->get();

            if ($categories->isNotEmpty()) {
                // Stocker les noms des catégories sélectionnées
                $this->categorie = $categories->pluck('categorie_produit_services')->toArray();

                // Récupération des produits avec des références uniques
                $this->produits = ProduitService::whereIn('categorie_id', $this->selectedCategories)
                    ->orderBy('reference')
                    ->get()
                    ->unique('reference')
                    ->values(); // Réindexation des clés
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
        $selectedProduct = ProduitService::find($productId);


        if ($selectedProduct) {
            // Remplir les propriétés avec les détails du produit sélectionné
            $this->categorie = $selectedProduct->categorie->categorie_produit_services;
            $this->reference = $selectedProduct->reference;
            $this->name = $selectedProduct->name;
            $this->type = $selectedProduct->type;
            $this->conditionnement = $selectedProduct->condProd;
            $this->format = $selectedProduct->formatProd;
            $this->poids = $selectedProduct->poids;
            $this->particularite = $selectedProduct->Particularite;
            $this->origine = $selectedProduct->origine;
            $this->specification = $selectedProduct->specification;

            $this->photoProd1 = $selectedProduct->photoProd1;
            $this->photoProd2 = $selectedProduct->photoProd2;
            $this->photoProd3 = $selectedProduct->photoProd3;
            $this->photoProd4 = $selectedProduct->photoProd4;

            $this->qualification = $selectedProduct->experience;
            $this->specialite = $selectedProduct->specialite;
            $this->descrip = $selectedProduct->description;
            $this->Duree = $selectedProduct->duree;
            $this->disponibilite = $selectedProduct->disponible;
            $this->lieu_intervention = $selectedProduct->lieu;

            $this->qteProd_min = '';
            $this->qteProd_max = '';
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
        $this->particularite = '';
        $this->origine = '';
        $this->qteProd_min = '';
        $this->qteProd_max = '';
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
        if ($this->isSubmitting) {
            return;
        }

        $this->isSubmitting = true;

        $this->validate([
            'categorie' => 'required|string',
            'type' => 'required|string|in:Produit,Service',
            'reference' => 'required|string|unique:produit_services,reference,NULL,id,user_id,' . auth()->id(),
            'name' => 'required|string|max:255',
            //produits
            'conditionnement' => $this->type == 'Produit' ? 'required|string|max:255' : 'nullable|string',
            'format' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'particularite' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'origine' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            'qteProd_min' => $this->type == 'Produit' ? 'required|string' : 'nullable|integer',
            'qteProd_max' => $this->type == 'Produit' ? 'required|string' : 'nullable|integer',
            //
            'prix' => 'required|integer',
            //service
            'specialite' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'qualification' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'descrip' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'Duree' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'disponibilite' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'lieu_intervention' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            //

            //photo
            'photoProd1' => $this->photoProd1 ? '' : 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd2' => $this->photoProd2 ? '' : 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd3' => $this->photoProd3 ? '' : 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd4' => $this->photoProd4 ? '' : 'nullable|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
        ], [
            'name.required' => 'Le nom est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'name.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
            'reference.unique' => 'Vous ne pouvez pas inscrire deux fois le même nom de produit',
            // Messages d'erreur pour les photos
            'photoProd1.required' => 'La photo principale est requise.',
            'photoProd1.image' => 'La photo principale doit être une image.',
            'photoProd1.mimes' => 'La photo principale doit être au format jpeg, png, jpg ou gif.',
            'photoProd1.dimensions' => 'La photo principale doit avoir des dimensions d\'au moins 500x400 pixels.',

            'photoProd2.required' => 'La deuxième photo est requise.',
            'photoProd2.image' => 'La deuxième photo doit être une image.',
            'photoProd2.mimes' => 'La deuxième photo doit être au format jpeg, png, jpg ou gif.',
            'photoProd2.dimensions' => 'La deuxième photo doit avoir des dimensions d\'au moins 500x400 pixels.',

            'photoProd3.required' => 'La troisième photo est requise.',
            'photoProd3.image' => 'La troisième photo doit être une image.',
            'photoProd3.mimes' => 'La troisième photo doit être au format jpeg, png, jpg ou gif.',
            'photoProd3.dimensions' => 'La troisième photo doit avoir des dimensions d\'au moins largeur=  500x longeur = 400 pixels.',

            'photoProd4.required' => 'La quatrième photo est requise.',
            'photoProd4.image' => 'La quatrième photo doit être une image.',
            'photoProd4.mimes' => 'La quatrième photo doit être au format jpeg, png, jpg ou gif.',
            'photoProd4.dimensions' => 'La quatrième photo doit avoir des dimensions d\'au moins 500x400 pixels.',
        ]);


        try {
            // Création de la catégorie si elle n'existe pas encore
            if ($this->categorie) {
                $categorie = CategorieProduits_Servives::firstOrCreate([
                    'categorie_produit_services' => $this->categorie,
                ]);
            }

            $produitService = ProduitService::create([
                'type' => $this->type,
                'reference' => $this->reference,
                'name' => $this->name, // Adjusted for 'Produit'
                //produit
                'condProd' => $this->type === 'Produit' ? $this->conditionnement : null,
                'formatProd' => $this->type === 'Produit' ? $this->format : null,
                'Particularite' => $this->type === 'Produit' ? $this->particularite : null,
                'origine' => $this->type === 'Produit' ? $this->origine : null,
                'qteProd_min' => $this->type === 'Produit' ? $this->qteProd_min : null,
                'qteProd_max' => $this->type === 'Produit' ? $this->qteProd_max : null,
                //
                'prix' => $this->prix,
                //service
                'specialite' => $this->type === 'Service' ? $this->specialite : null,
                'experience' => $this->type === 'Service' ? $this->qualification : null,
                'description' => $this->type === 'Service' ? $this->descrip : null,
                'duree' => $this->type === 'Service' ? $this->Duree : null,
                'disponible' => $this->type === 'Service' ? $this->disponibilite : null,
                'lieu' => $this->type === 'Service' ? $this->lieu_intervention : null,

                'user_id' => auth()->id(),
                'categorie_id' => $categorie->id ?? null,
            ]);



            // Gestion des photos

            if ($this->photoProd1 && is_string($this->photoProd1)) {
                // If photoProd1 is a string (from input field), use it directly
                $produitService->update(['photoProd1' => $this->photoProd1]);
                $produitService->update(['photoProd2' => $this->photoProd1]);
                $produitService->update(['photoProd3' => $this->photoProd1]);
                $produitService->update(['photoProd4' => $this->photoProd1]);
            } else {
                $this->handlePhotoUpload($produitService, 'photoProd1');
            }
            $this->handlePhotoUpload($produitService, 'photoProd2');
            $this->handlePhotoUpload($produitService, 'photoProd3');
            $this->handlePhotoUpload($produitService, 'photoProd4');

            session()->flash('message', 'Produit ou service ajouté avec succès!');
            $this->dispatch('formSubmitted', 'Enregistrement du produit effectué avec success');

            $this->resetForm(); // Réinitialise le formulaire après la soumission

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'ajout du produit ou service: ' . $e->getMessage());
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout du produit ou service.');
        } finally {
            $this->isSubmitting = false;
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
        $this->poids = '';
        $this->particularite = '';
        $this->origine = '';
        $this->qteProd_min = '';
        $this->qteProd_max = '';
        $this->specification = '';
        $this->prix = '';
        $this->qualification = '';
        $this->specialite = '';
        $this->descrip = '';
        $this->Quantite = '';

        // Réinitialiser les catégories si nécessaire
        $this->categories = CategorieProduits_Servives::all();
        // Réinitialiser les photos
        $this->photoProd1 = null;
        $this->photoProd2 = null;
        $this->photoProd3 = null;
        $this->photoProd4 = null;
    }

    protected function handlePhotoUpload($produitService, $photoField)
    {
        // Check if the property is an instance of UploadedFile
        if ($this->$photoField instanceof \Illuminate\Http\UploadedFile) {
            $photo = $this->$photoField;
            $photoName = Carbon::now()->timestamp . '_' . $photoField . '.' . $photo->extension();


            // Redimensionner l'image à 300x300 pixels
            $imageResized = Image::make($photo->getRealPath());
            // Redimensionner l'image en la recadrant pour obtenir exactement 500x400 pixels
            $imageResized->fit(500, 400);


            // Sauvegarder l'image redimensionnée
            $imageResized->save(public_path('post/all/' . $photoName), 90); // Corriger le chemin avec '/' entre 'post' et le nom du fichier


            // $photo->storeAs('all', $photoName); // Ensure to specify a directory
            $produitService->update([$photoField => $photoName]);
        }
    }

    public function render()
    {
        return view('livewire.ajout-produit-services');
    }
}

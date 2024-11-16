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
    public $Quantite  = '';
    public $descrip  = '';

    //
    public $depart  = '';
    public $ville  = '';
    public $commune  = '';
    public $pays  = '';
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

    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
        $this->produits = collect(); // Ensure it's an empty Collection
        $this->fetchCountries();
        $this->user = User::find(auth()->id());

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
            $this->categorie = $selectedProduct->categorie->categorie_produit_services;
            $this->reference = $selectedProduct->reference;
            $this->name = $selectedProduct->name;
            $this->type = $selectedProduct->type;
            $this->conditionnement = $selectedProduct->condProd;
            $this->format = $selectedProduct->formatProd;
            $this->particularite = $selectedProduct->Particularite;
            $this->origine = $selectedProduct->origine;
            $this->specification = $selectedProduct->specification;

            // $this->photoProd1 = $selectedProduct->photoProd1;
            // $this->photoProd2 = $selectedProduct->photoProd2;
            // $this->photoProd3 = $selectedProduct->photoProd3;
            // $this->photoProd4 = $selectedProduct->photoProd4;

            $this->qualification = $selectedProduct->qalifServ;
            $this->specialite = $selectedProduct->sepServ;
            $this->descrip = $selectedProduct->description;

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
            'specification' => $this->type == 'Produit' ? 'required|string' : 'nullable|string',
            //
            'prix' => 'required|integer',
            //service
            'qualification' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'specialite' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'descrip' => $this->type == 'Service' ? 'required|string' : 'nullable|string',
            'Quantite' => $this->type == 'Service' ? 'required|integer' : 'nullable|integer',
            //
            // 'selectedSous_region' => 'required|string',
            // 'selectedContinent' => 'required|string',
            // 'pays' => 'string',
            // 'depart' => 'string',
            // 'ville' => 'string',
            // 'commune' => 'string',
            //photo
            'photoProd1' => 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd2' => 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd3' => 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
            'photoProd4' => 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=500,min_height=400',
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

        // 'photoProd1' => 'required|image|mimes:jpeg,png,jpg,gif|dimensions:min_width=100,min_height=200,max_width=1000,max_height=1000',

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
                'specification' => $this->type === 'Produit' ? $this->specification : null,
                //
                'prix' => $this->prix,
                //service
                'qalifServ' => $this->type === 'Service' ? $this->qualification : null,
                'sepServ' => $this->type === 'Service' ? $this->specialite : null,
                'description' => $this->type === 'Service' ? $this->descrip : null,
                'quantite' => $this->type === 'Service' ? $this->Quantite : null,
                //localisation
                'continent' => $this->user->continent,
                'sous_region' => $this->user->sous_region,
                'pays' => $this->user->country,
                'zonecoServ' => $this->user->active_zone,
                'villeServ' => $this->user->ville,
                'comnServ' => $this->user->commune,
                'user_id' => auth()->id(),
                'categorie_id' => $categorie->id ?? null,
            ]);

            // Gestion des photos

            if ($this->photoProd1 && is_string($this->photoProd1)) {
                // If photoProd1 is a string (from input field), use it directly
                $produitService->update(['photoProd1' => $this->photoProd1]);
            } else {
                $this->handlePhotoUpload($produitService, 'photoProd1');
            }
            $this->handlePhotoUpload($produitService, 'photoProd2');
            $this->handlePhotoUpload($produitService, 'photoProd3');
            $this->handlePhotoUpload($produitService, 'photoProd4');

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
        $this->depart = '';
        $this->ville = '';
        $this->commune = '';
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

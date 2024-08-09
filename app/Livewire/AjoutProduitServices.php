<?php

namespace App\Livewire;

use App\Models\CategorieProduits_Servives;
use App\Models\ProduitService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;


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
    public $qte_service  = '';
    public $depart  = '';
    public $ville  = '';
    public $commune  = '';
    public $produits = [];
    public $selectedCategories = [];
    public $selectedProduits = [];
    //photo
    public $photoProd1;
    public $photoProd2;
    public $photoProd3;
    public $photoProd4;


    public function mount()
    {
        // Récupère toutes les catégories
        $this->categories = CategorieProduits_Servives::all();
        $this->produits = collect(); // Ensure it's an empty Collection

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
            $this->name = $selectedProduct->name;
            $this->conditionnement = $selectedProduct->condProd;
            $this->format = $selectedProduct->formatProd;
            $this->particularite = $selectedProduct->Particularite;
            $this->origine = $selectedProduct->origine;
            $this->qteProd_min = $selectedProduct->qteProd_min;
            $this->qteProd_max = $selectedProduct->qteProd_max;
            $this->specification = $selectedProduct->specification;
            $this->prix = $selectedProduct->prix;
            $this->qualification = ''; // Reset qualifications for services
            $this->specialite = ''; // Reset specialties for services
            $this->qte_service = ''; // Reset service quantity
        } else {
            // Réinitialiser les propriétés si aucun produit n'est trouvé
            $this->resetProductFields();
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
        $this->qte_service = '';
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
            //photo
            'photoProd1' => 'required|image|mimes:jpeg,png,jpg,gif',
            'photoProd2' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'photoProd3' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'photoProd4' => 'nullable|image|mimes:jpeg,png,jpg,gif',
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
                'specification' => $this->type === 'Produit' ? $this->specification : null,
                //
                'prix' => $this->prix,
                //service
                'qalifServ' => $this->type === 'Service' ? $this->qualification : null,
                'sepServ' => $this->type === 'Service' ? $this->specialite : null,
                'qteServ' => $this->type === 'Service' ? $this->qte_service : null,
                //
                'zonecoServ' => $this->depart,
                'villeServ' => $this->ville,
                'comnServ' => $this->commune,
                'user_id' => auth()->id(),
                'categorie_id' => $categorie->id ?? null,
            ]);

            // Gestion des photos
            $this->handlePhotoUpload($produitService, 'photoProd1');
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
        $this->qte_service = '';
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
        if ($this->$photoField) {
            $photoName = Carbon::now()->timestamp . '_' . $photoField . '.' . $this->$photoField->extension();
            $this->$photoField->storeAs('all', $photoName); // Assurez-vous de spécifier un répertoire
            $produitService->update([$photoField => $photoName]);
        }
    }
    public function render()
    {
        return view('livewire.ajout-produit-services');
    }
}

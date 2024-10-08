<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Psap;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Livraisons; // Correctly importing the Livraisons model

class PostulerComponent extends Component
{
    use WithFileUploads;

    public $experience;
    public $vehicle;
    public $vehicle2;
    public $vehicle3;
    public $identity;
    public $permis;
    public $assurance;
    public $etat = 'En cours';

    public $livraison;
    public $zone;

    // Localisation properties
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
        'Amérique Centrale',
        'Amérique du Sud',
        'Caraïbes',
        'Asie de l\'Est',
        'Asie du Sud',
        'Asie du Sud-Est',
        'Asie Centrale',
        'Asie de l\'Ouest',
        'Europe de l\'Est',
        'Europe de l\'Ouest',
        'Europe du Nord',
        'Europe du Sud',
        'Australie et Nouvelle-Zélande',
        'Mélanésie',
        'Polynésie',
        'Micronésie'
    ];
    public $depart = '';
    public $pays;
    public $ville = '';
    public $localite;
    public $countries = [];

    public $psap;

    public function mount()
    {
        $this->livraison = Livraisons::where('user_id', Auth::id())->first();

        $this->psap = Psap::where('user_id', Auth::id())->first();
        
        $this->fetchCountries();
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
    public function submit()
    {
        $this->validate([
            'experience' => 'required|string',
            'vehicle' => 'required|string',
            'vehicle2' => 'nullable|string',
            'vehicle3' => 'nullable|string',
            'zone' => 'required|string',
            'selectedContinent' => 'required|string',
            'selectedSous_region' => 'required|string',
            'pays' => 'required|string',
            'depart' => 'required|string',
            'ville' => 'required|string',
            'localite' => 'required|string',
            'identity' => 'required|file|mimes:jpeg,png,pdf',
            'permis' => 'required|file|mimes:jpeg,png,pdf',
            'assurance' => 'required|file|mimes:jpeg,png,pdf',
        ]);

        $livraison = new Livraisons();
        $livraison->user_id = Auth::id();
        $livraison->experience = $this->experience;
        $livraison->vehicle = $this->vehicle;
        $livraison->vehicle2 = $this->vehicle2;
        $livraison->vehicle3 = $this->vehicle3;
        $livraison->zone = $this->zone;
        $livraison->continent = $this->selectedContinent;
        $livraison->sous_region = $this->selectedSous_region;
        $livraison->departe = $this->depart;
        $livraison->ville = $this->ville;
        $livraison->commune = $this->localite;
        $livraison->etat = $this->etat;
        $livraison->pays = $this->pays;

        $livraison->save();


        // Gestion des photos
        $this->handlePhotoUpload($livraison, 'identity');
        $this->handlePhotoUpload($livraison, 'permis');
        $this->handlePhotoUpload($livraison, 'assurance');

        session()->flash('message', 'Votre demande a été soumise avec succès!');

        // Clear form fields after submission
        $this->reset([
            'experience',
            'vehicle',
            'selectedContinent',
            'selectedSous_region',
            'pays',
            'depart',
            'ville',
            'localite',
            'identity',
            'permis',
            'assurance'
        ]);
    }

    public function submitPsap()
{
    // Validation avec messages d'erreur personnalisés
    $this->validate([
        'experience' => 'required|string',
        'selectedContinent' => 'required|string',
        'selectedSous_region' => 'required|string',
        'pays' => 'required|string',
        'depart' => 'required|string',
        'ville' => 'required|string',
        'localite' => 'required|string',
        'identity' => 'required|file|mimes:jpeg,png,pdf',
        'permis' => 'required|file|mimes:jpeg,png,pdf',
        'assurance' => 'required|file|mimes:jpeg,png,pdf',
    ], [
        'experience.required' => 'Le champ expérience est obligatoire.',
        'experience.string' => 'Le champ expérience doit être une chaîne de caractères.',
        'selectedContinent.required' => 'Le champ continent est obligatoire.',
        'selectedContinent.string' => 'Le champ continent doit être une chaîne de caractères.',
        'selectedSous_region.required' => 'Le champ sous-région est obligatoire.',
        'selectedSous_region.string' => 'Le champ sous-région doit être une chaîne de caractères.',
        'pays.required' => 'Le champ pays est obligatoire.',
        'pays.string' => 'Le champ pays doit être une chaîne de caractères.',
        'depart.required' => 'Le champ département est obligatoire.',
        'depart.string' => 'Le champ département doit être une chaîne de caractères.',
        'ville.required' => 'Le champ ville est obligatoire.',
        'ville.string' => 'Le champ ville doit être une chaîne de caractères.',
        'localite.required' => 'Le champ localité est obligatoire.',
        'localite.string' => 'Le champ localité doit être une chaîne de caractères.',
        'identity.required' => 'Le fichier d\'identité est obligatoire.',
        'identity.file' => 'Le champ identité doit être un fichier.',
        'identity.mimes' => 'Le fichier d\'identité doit être au format jpeg, png ou pdf.',
        'permis.required' => 'Le fichier de permis est obligatoire.',
        'permis.file' => 'Le champ permis doit être un fichier.',
        'permis.mimes' => 'Le fichier de permis doit être au format jpeg, png ou pdf.',
        'assurance.required' => 'Le fichier d\'assurance est obligatoire.',
        'assurance.file' => 'Le champ assurance doit être un fichier.',
        'assurance.mimes' => 'Le fichier d\'assurance doit être au format jpeg, png ou pdf.',
    ]);

    // Création du modèle PSAP
    $psap = new Psap();
    $psap->user_id = Auth::id();
    $psap->experience = $this->experience;
    $psap->continent = $this->selectedContinent;
    $psap->sous_region = $this->selectedSous_region;
    $psap->depart = $this->depart;
    $psap->ville = $this->ville;
    $psap->localite = $this->localite;
    $psap->pays = $this->pays;
    $psap->etat = "En cours";
  

    $psap->save();

    // Gestion des photos
    $this->handlePhotoUpload($psap, 'identity');
    $this->handlePhotoUpload($psap, 'permis');
    $this->handlePhotoUpload($psap, 'assurance');

    session()->flash('message', 'PSAP ajouté avec succès!');

    // Réinitialiser les champs du formulaire
    $this->reset([
        'experience',
        'selectedContinent',
        'selectedSous_region',
        'pays',
        'depart',
        'ville',
        'localite',
        'identity',
        'permis',
        'assurance'
    ]);
}


    protected function handlePhotoUpload($livreur, $photoField)
    {
        if ($this->$photoField) {
            $photoName = Carbon::now()->timestamp . '_' . $photoField . '.' . $this->$photoField->extension();
            $this->$photoField->storeAs('all', $photoName); // Assurez-vous de spécifier un répertoire
            $livreur->update([$photoField => $photoName]);
        }
    }
    public function render()
    {
        return view('livewire.postuler');
    }
}

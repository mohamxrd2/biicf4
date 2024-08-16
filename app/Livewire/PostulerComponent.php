<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Livraisons;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PostulerComponent extends Component
{
    use WithFileUploads;

    public $experience;
    public $license;
    public $vehicle;
    public $matricule;
    public $availability;
    public $zone;
    public $identity;
    public $permis;
    public $assurance;
    public $etat = 'En cours';


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

    protected $rules = [
        'experience' => 'required|string',
        'license' => 'required|string',
        'vehicle' => 'required|string',
        'matricule' => 'required|string',
        'availability' => 'required|string',
        'selectedContinent' => 'required|string',
        'selectedSous_region' => 'required|string',
        // 'pays' => 'required|string',
        'depart' => 'required|string',
        'ville' => 'required|string',
        'localite' => 'required|string',
        'identity' => 'required|file|mimes:jpeg,png,pdf',
        'permis' => 'required|file|mimes:jpeg,png,pdf',
        'assurance' => 'required|file|mimes:jpeg,png,pdf',
    ];
    

    public function submit()
    {
        $this->validate();

        // Save the data in the database
        $livraison = Livraisons::create([
            'user_id' => Auth::id(),
            'experience' => $this->experience,
            'license' => $this->license,
            'vehicle' => $this->vehicle,
            'matricule' => $this->matricule,
            'availability' => $this->availability,
            'continent' => $this->selectedContinent,
            'Sous_Region' => $this->selectedSous_region,
            // 'pays' => $this->pays,
            'departe' => $this->depart,
            'ville' => $this->ville,
            'commune' => $this->localite,
            'etat' => $this->etat,
        ]);


        // Gestion des photos
        $this->handlePhotoUpload($livraison, 'identity');
        $this->handlePhotoUpload($livraison, 'permis');
        $this->handlePhotoUpload($livraison, 'assurance');

        session()->flash('message', 'Votre demande a été soumise avec succès!');

        // Clear form fields after submission
        $this->reset([
            'experience',
            'license',
            'vehicle',
            'matricule',
            'availability',
            'selectedContinent',
            'selectedSous_region',
            // 'pays' ,
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

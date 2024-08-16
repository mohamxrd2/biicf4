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
    public $comments;
    public $identity;
    public $permis;
    public $assurance;
    public $etat = 'En cours';

    public $livraison;

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
    public $pays = '';
    public $ville = '';
    public $commune = '';

    protected $rules = [
        'experience' => 'required|string',
        'license' => 'required|string',
        'vehicle' => 'required|string',
        'matricule' => 'required|string',
        'availability' => 'required|string',
        'zone' => 'required|string',
        'comments' => 'nullable|string',
        'identity' => 'required|file|mimes:jpeg,png,pdf',
        'permis' => 'required|file|mimes:jpeg,png,pdf',
        'assurance' => 'required|file|mimes:jpeg,png,pdf',
    ];

    public function submit()
    {
        $this->validate();

        // Handle file uploads
        $identityPath = $this->identity->store('identities', 'public');
        $permisPath = $this->permis->store('permis', 'public');
        $assurancePath = $this->assurance->store('assurances', 'public');

        // Save the data in the database
        $livraison = new Livraisons();
        $livraison->user_id = Auth::id();
        $livraison->experience = $this->experience;
        $livraison->license = $this->license;
        $livraison->vehicle = $this->vehicle;
        $livraison->matricule = $this->matricule;
        $livraison->availability = $this->availability;
        $livraison->zone = $this->zone;
        $livraison->comments = $this->comments;
        $livraison->identity = $identityPath;
        $livraison->permis = $permisPath;
        $livraison->assurance = $assurancePath;
        $livraison->etat = $this->etat;
        $livraison->created_at = Carbon::now();

        $livraison->save();

        session()->flash('message', 'Votre demande a été soumise avec succès!');

        // Clear form fields after submission
        $this->reset([
            'experience',
            'license',
            'vehicle',
            'matricule',
            'availability',
            'zone',
            'comments',
            'identity',
            'permis',
            'assurance'
        ]);
    }

    public function render()
    {
        return view('livewire.postuler');
    }
}

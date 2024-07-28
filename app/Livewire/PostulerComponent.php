<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\livraisons;
use Illuminate\Support\Facades\Auth;

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
        'etat' => 'required|string',
    ];

    public function messages()
    {
        return [
            'experience.required' => 'Le champ expérience est obligatoire.',
            'license.required' => 'Le champ type de permis de conduire est obligatoire.',
            'vehicle.required' => 'Le champ véhicule possédé est obligatoire.',
            'matricule.required' => 'Le champ matricule du véhicule est obligatoire.',
            'availability.required' => 'Le champ disponibilités est obligatoire.',
            'zone.required' => 'Le champ zone de livraison est obligatoire.',
            'comments.string' => 'Le champ questions ou commentaires doit être une chaîne de caractères.',
            'identity.required' => 'Le champ pièce d\'identité est obligatoire.',
            'identity.file' => 'Le champ pièce d\'identité doit être un fichier.',
            'identity.mimes' => 'Le champ pièce d\'identité doit être un fichier de type : jpeg, png, pdf.',
            'permis.required' => 'Le champ permis de conduire est obligatoire.',
            'permis.file' => 'Le champ permis de conduire doit être un fichier.',
            'permis.mimes' => 'Le champ permis de conduire doit être un fichier de type : jpeg, png, pdf.',
            'assurance.required' => 'Le champ assurance du véhicule est obligatoire.',
            'assurance.file' => 'Le champ assurance du véhicule doit être un fichier.',
            'assurance.mimes' => 'Le champ assurance du véhicule doit être un fichier de type : jpeg, png, pdf.',
            'etat.required' => 'Le champ état est obligatoire.',
            'etat.string' => 'Le champ état doit être une chaîne de caractères.',
        ];
    }


    public function mount()
    {
        $this->livraison = livraisons::where('user_id', Auth::id())->first();
        
        if ($this->livraison) {
            $this->experience = $this->livraison->experience;
            $this->license = $this->livraison->license;
            $this->vehicle = $this->livraison->vehicle;
            $this->matricule = $this->livraison->matricule;
            $this->availability = $this->livraison->availability;
            $this->zone = implode(';', json_decode($this->livraison->zone, true));
            $this->comments = $this->livraison->comments;
            $this->identity = $this->livraison->identity;
            $this->permis = $this->livraison->permis;
            $this->assurance = $this->livraison->assurance;
            $this->etat = $this->livraison->etat;
        }
    }

    public function submit()
    {
        $this->validate();

        // Sauvegarde des fichiers
        $identityPath = $this->identity->store('identities');
        $permisPath = $this->permis->store('permis');
        $assurancePath = $this->assurance->store('assurances');

        livraisons::create([
            'user_id' => Auth::id(),
            'experience' => $this->experience,
            'license' => $this->license,
            'vehicle' => $this->vehicle,
            'matricule' => $this->matricule,
            'availability' => $this->availability,
            'zone' => json_encode(explode(';', $this->zone)), // Convertir en JSON
            'comments' => $this->comments,
            'identity' => $identityPath ?? 'null',
            'permis' => $permisPath ?? 'null',
            'assurance' => $assurancePath ?? 'null',
            'etat' => $this->etat,
        ]);

        // Réinitialiser les champs du formulaire
        $this->reset([
            'experience', 'license', 'vehicle', 'matricule', 
            'availability', 'zone', 'comments', 'identity', 
            'permis', 'assurance', 'etat'
        ]);

        session()->flash('message', 'Candidature soumise avec succès.');
    }

    public function render()
    {
        return view('livewire.postuler');
    }
}

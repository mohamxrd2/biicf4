<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\livraisons;
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

        $livraisons = $this->livraison ?: new livraisons();
        $livraisons->user_id = Auth::id();
        $livraisons->experience = $this->experience;
        $livraisons->license = $this->license;
        $livraisons->vehicle = $this->vehicle;
        $livraisons->matricule = $this->matricule;
        $livraisons->availability = $this->availability;
        $livraisons->zone = json_encode(explode(';', $this->zone));
        $livraisons->comments = $this->comments;
        $livraisons->etat = $this->etat;

        // Handle file uploads
        if ($this->identity) {
            $identityImageName = Carbon::now()->timestamp . '_1.' . $this->identity->extension();
            $this->identity->storeAs('all', $identityImageName);
            $livraisons->identity = $identityImageName;
        }

        if ($this->permis) {
            $permisImageName = Carbon::now()->timestamp . '_2.' . $this->permis->extension();
            $this->permis->storeAs('all', $permisImageName);
            $livraisons->permis = $permisImageName;
        }

        if ($this->assurance) {
            $assuranceImageName = Carbon::now()->timestamp . '_3.' . $this->assurance->extension();
            $this->assurance->storeAs('all', $assuranceImageName);
            $livraisons->assurance = $assuranceImageName;
        }

        $livraisons->save();

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

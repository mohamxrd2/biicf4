<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Psap;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Models\Livraisons; // Correctly importing the Livraisons model
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

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

    public $localite;
    protected $layout = 'components.layouts.app';

    public $psap;

    public function mount()
    {
        $this->livraison = Livraisons::where('user_id', Auth::id())->first();

        $this->psap = Psap::where('user_id', Auth::id())->first();
    }


    public function submit()
    {
        try {
            // Valider les champs du formulaire
            $validatedData = $this->validate([
                'experience' => 'required|string',
                'vehicle' => 'required|string',
                'vehicle2' => 'nullable|string',
                'vehicle3' => 'nullable|string',
                'zone' => 'required|string',
                'identity' => 'required|file|mimes:jpeg,png,pdf|max:2048', // Limite la taille du fichier à 2MB
                'permis' => 'required|file|mimes:jpeg,png,pdf|max:2048',
                'assurance' => 'required|file|mimes:jpeg,png,pdf|max:2048',
            ]);

            // Récupérer l'utilisateur connecté
            $user = Auth::user();

            if (!$user) {
                session()->flash('error', 'Utilisateur non authentifié.');
                return;
            }

            // Créer une nouvelle instance de Livraisons
            $livraison = new Livraisons();

            // Assigner les valeurs de l'utilisateur connecté
            $livraison->user_id = $user->id;
            $livraison->continent = $user->continent;
            $livraison->sous_region = $user->sous_region;
            $livraison->pays = $user->country;
            $livraison->departe = $user->departe;
            $livraison->ville = $user->ville;
            $livraison->commune = $user->commune;

            // Assigner les données du formulaire
            $livraison->experience = $validatedData['experience'];
            $livraison->vehicle = $validatedData['vehicle'];
            $livraison->vehicle2 = $validatedData['vehicle2'];
            $livraison->vehicle3 = $validatedData['vehicle3'];
            $livraison->zone = $validatedData['zone'];
            $livraison->etat = $this->etat ?? null; // Assurez-vous que "etat" est facultatif
            $livraison->pays = $this->pays ?? null;

            // Sauvegarder la livraison
            $livraison->save();

            // Gestion des uploads de fichiers
            $this->handlePhotoUpload($livraison, 'identity');
            $this->handlePhotoUpload($livraison, 'permis');
            $this->handlePhotoUpload($livraison, 'assurance');

            // Message de succès
            session()->flash('message', 'Votre demande a été soumise avec succès!');

            // Réinitialiser les champs du formulaire
            $this->reset([
                'experience',
                'vehicle',
                'vehicle2',
                'vehicle3',
                'identity',
                'permis',
                'assurance'
            ]);

            $this->dispatch('formSubmitted', 'Enregistrement effectué avec success');
            // actuliser la page
            $this->livraison = Livraisons::where('user_id', Auth::id())->first();
            $this->restForm();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Gérer les erreurs de validation
            session()->flash('error', 'Veuillez corriger les erreurs ci-dessous.');
            $this->addError('validation', $e->getMessage());
        } catch (Exception $e) {
            // Gérer d'autres erreurs
            session()->flash('error', 'Une erreur est survenue lors du traitement de votre demande.');
            // Log de l'erreur pour débogage
            Log::error('Erreur lors de la soumission : ' . $e->getMessage());
        }
    }


    public function submitPsap()
    {
        // Validation avec messages d'erreur personnalisés
        $this->validate([
            'experience' => 'required|string',

            'identity' => 'required|file|mimes:jpeg,png,pdf',
            'permis' => 'required|file|mimes:jpeg,png,pdf',
            'assurance' => 'required|file|mimes:jpeg,png,pdf',
        ], [
            'experience.required' => 'Le champ expérience est obligatoire.',
            'experience.string' => 'Le champ expérience doit être une chaîne de caractères.',

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

        $psap->etat = "En cours";


        $psap->save();

        // Gestion des photos
        $this->handlePhotoUpload($psap, 'identity');
        $this->handlePhotoUpload($psap, 'permis');
        $this->handlePhotoUpload($psap, 'assurance');

        session()->flash('message', 'PSAP ajouté avec succès!');

        // Réinitialiser les champs du formulaire
        $this->resetForm();
    }
    public function removeIdentity()
    {
        $this->identity = null;
    }

    public function removePermis()
    {
        $this->permis = null;
    }

    public function removeAssurance()
    {
        $this->assurance = null;
    }

    public function resetForm()
    {
        $this->reset(['experience', 'identity', 'permis', 'assurance', 'experience', 'vehicle', 'vehicle2', 'vehicle3', 'zone',]);
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

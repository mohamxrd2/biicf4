<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;
use Livewire\WithFileUploads; // Ajout pour gérer les fichiers
use Illuminate\Support\Facades\Auth;

class AddProjetFinance extends Component
{
    use WithFileUploads; // Utilisation du trait pour gérer les fichiers

    public $montant;
    public $taux;
    public $description;
    public $categorie;
    public $type_financement;
    public $statut = 'en attente'; // Statut par défaut

    public $durer; // Nouvel attribut pour la date limite

    // Propriétés pour les photos
    public $photo1, $photo2, $photo3, $photo4, $photo5;

    public $isSubmitting = false; // Pour indiquer si la soumission est en cours
    public $successMessage = '';  // Message de succès

    // Définition des règles de validation
    protected $rules = [
        'montant' => 'required|numeric',
        'taux' => 'required|numeric',
        'description' => 'required|string',
        'categorie' => 'required|string|max:100',
        'type_financement' => 'required|string|max:100',
        'photo1' => 'required|image|max:2048', // Photo 1 obligatoire
        'photo2' => 'nullable|image|max:2048', // Photos 2-5 facultatives
        'photo3' => 'nullable|image|max:2048',
        'photo4' => 'nullable|image|max:2048',
        'photo5' => 'nullable|image|max:2048',
        'durer' => 'required|date',
    ];

    // Messages personnalisés
    public function messages()
    {
        return [
            'montant.required' => 'Le montant est requis.',
            'montant.numeric' => 'Le montant doit être un nombre.',
            'taux.required' => 'Le taux est requis.',
            'taux.numeric' => 'Le taux doit être un nombre.',
            'description.required' => 'La description est requise.',
            'description.string' => 'La description doit être une chaîne de caractères.',
            'categorie.required' => 'La catégorie est requise.',
            'categorie.string' => 'La catégorie doit être une chaîne de caractères.',
            'categorie.max' => 'La catégorie ne doit pas dépasser 100 caractères.',
            'type_financement.required' => 'Le type de financement est requis.',
            'type_financement.string' => 'Le type de financement doit être une chaîne de caractères.',
            'type_financement.max' => 'Le type de financement ne doit pas dépasser 100 caractères.',
            'photo1.required' => 'La photo 1 est requise.', // Message d'erreur pour photo 1
            'photo1.image' => 'La photo 1 doit être une image.',
            'photo1.max' => 'La photo 1 ne doit pas dépasser 2MB.',
            'photo2.image' => 'La photo 2 doit être une image.',
            'photo2.max' => 'La photo 2 ne doit pas dépasser 2MB.',
            'photo3.image' => 'La photo 3 doit être une image.',
            'photo3.max' => 'La photo 3 ne doit pas dépasser 2MB.',
            'photo4.image' => 'La photo 4 doit être une image.',
            'photo4.max' => 'La photo 4 ne doit pas dépasser 2MB.',
            'photo5.image' => 'La photo 5 doit être une image.',
            'photo5.max' => 'La photo 5 ne doit pas dépasser 2MB.',
            'durer.required' => 'La date limite est requise.',
            'durer.date' => 'La date limite doit être une date valide.',
        ];
    }

    public function mount()
    {
        $this->resetForm(); // Réinitialiser les champs du formulaire par défaut
    }

    // Fonction pour soumettre le formulaire
    public function submit()
    {
        // Vérification pour éviter les soumissions multiples
        if ($this->isSubmitting) {
            return;
        }

        // Validation des champs
        $this->validate();

        // Indicateur de soumission en cours
        $this->isSubmitting = true;

        try {
            // Création du projet avec le statut 'en attente'
            $projet = Projet::create([
                'montant' => $this->montant,
                'taux' => $this->taux,
                'description' => $this->description,
                'categorie' => $this->categorie,
                'type_financement' => $this->type_financement,
                'statut' => $this->statut, // Assurez-vous que le statut soit défini ici
                'durer' => $this->durer,
                'id_user' => auth()->id(), // ID de l'utilisateur connecté
            ]);

            // Gestion des photos
            if ($this->photo1) {
                $this->photo1->storeAs('photos/' . $projet->id, 'photo1.' . $this->photo1->getClientOriginalExtension());
                $projet->photo1 = 'photos/' . $projet->id . '/photo1.' . $this->photo1->getClientOriginalExtension();
            }
            if ($this->photo2) {
                $this->photo2->storeAs('photos/' . $projet->id, 'photo2.' . $this->photo2->getClientOriginalExtension());
                $projet->photo2 = 'photos/' . $projet->id . '/photo2.' . $this->photo2->getClientOriginalExtension();
            }
            if ($this->photo3) {
                $this->photo3->storeAs('photos/' . $projet->id, 'photo3.' . $this->photo3->getClientOriginalExtension());
                $projet->photo3 = 'photos/' . $projet->id . '/photo3.' . $this->photo3->getClientOriginalExtension();
            }
            if ($this->photo4) {
                $this->photo4->storeAs('photos/' . $projet->id, 'photo4.' . $this->photo4->getClientOriginalExtension());
                $projet->photo4 = 'photos/' . $projet->id . '/photo4.' . $this->photo4->getClientOriginalExtension();
            }
            if ($this->photo5) {
                $this->photo5->storeAs('photos/' . $projet->id, 'photo5.' . $this->photo5->getClientOriginalExtension());
                $projet->photo5 = 'photos/' . $projet->id . '/photo5.' . $this->photo5->getClientOriginalExtension();
            }

            // Sauvegarder les chemins des photos dans le projet
            $projet->save();

            // Réinitialiser le formulaire et les erreurs
            $this->resetForm();
            $this->resetErrorBag();

            // Message de succès
            $this->successMessage = 'Le projet a été ajouté avec succès !';

        } catch (\Exception $e) {
            // Si une erreur survient, réinitialiser l'indicateur de soumission
            $this->addError('submitError', 'Une erreur est survenue lors de la soumission : ' . $e->getMessage());
        }

        // Réinitialiser l'indicateur de soumission
        $this->isSubmitting = false;
    }

    // Fonction pour réinitialiser les champs du formulaire
    public function resetForm()
    {
        $this->montant = '';
        $this->taux = '';
        $this->description = '';
        $this->categorie = '';
        $this->type_financement = '';
        $this->statut = 'en attente'; // Remettre le statut par défaut
        $this->durer = ''; // Réinitialiser la date limite
        $this->photo1 = null;
        $this->photo2 = null;
        $this->photo3 = null;
        $this->photo4 = null;
        $this->photo5 = null; // Réinitialiser les photos
        $this->successMessage = '';
    }

    // Fonction pour afficher la vue associée
    public function render()
    {
        return view('livewire.add-projet-finance');
    }
}
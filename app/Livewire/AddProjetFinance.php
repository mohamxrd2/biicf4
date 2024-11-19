<?php

namespace App\Livewire;

use App\Models\Investisseur;
use App\Models\User;
use App\Models\Projet;
use App\Notifications\DemandeCreditProjetNotification;
use Livewire\Component;
use App\Models\CrediScore;
use App\Models\UserPromir;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Notifications\DemandeCreditNotification;
use Carbon\Carbon; // N'oublie pas d'importer Carbon
use Illuminate\Support\Facades\Notification;
use Livewire\WithFileUploads; // Ajout pour gérer les fichiers

class AddProjetFinance extends Component
{
    use WithFileUploads; // Utilisation du trait pour gérer les fichiers

    public $name;
    public $montant;
    public $taux;
    public $description;
    public $categorie;
    public $type_financement;
    public $portionActions;
    public $portionObligations;
    public $mode_recouvre;
    public $nombreActions;
    public $durerFin;
    public $statut = 'en attente'; // Statut par défaut
    public $durer; // Nouvel attribut pour la date limite

    // Propriétés pour les photos
    public $photo1, $photo2, $photo3, $photo4, $photo5;

    public $isSubmitting = false; // Pour indiquer si la soumission est en cours
    public $successMessage = '';  // Message de succès

    public $message = '';

    public $isEligible = false;


    public $search = '';   // Champ de recherche
    public $users = [];    // Liste des utilisateurs trouvés
    public $user_id;       // ID de l'utilisateur sélectionné
    public $username_direct;

    public $bailleur_groupé;

    // Définition des règles de validation
    protected $rules = [
        'name' => 'required|string|max:255',
        'montant' => 'required|numeric',
        'taux' => 'required|numeric',
        'description' => 'required|string',
        'categorie' => 'required|string|max:100',
        'portionActions' => 'nullable|numeric',
        'portionObligations' => 'nullable|numeric',
        'nombreActions' => 'nullable|numeric',
        'user_id' => 'nullable|exists:investisseurs,user_id', // Assurez-vous que l'utilisateur sélectionné existe
        'type_financement' => 'required|string|max:100',
        'photo1' => 'required|image|max:2048', // Photo 1 obligatoire
        'photo2' => 'nullable|image|max:2048', // Photos 2-5 facultatives
        'photo3' => 'nullable|image|max:2048',
        'photo4' => 'nullable|image|max:2048',
        'photo5' => 'nullable|image|max:2048',
        'durer' => 'required|date',
        'durerFin' => 'required|date',
    ];

    // Messages personnalisés
    public function messages()
    {
        return [
            'name.required' => 'Le nom du projet est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
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

    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Récupérer l'ID de l'utilisateur connecté
            $currentUserId = auth()->id();

            // Recherche des utilisateurs dont le nom d'utilisateur correspond à la saisie,
            // mais exclure l'utilisateur connecté
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId) // Exclure l'utilisateur connecté
                ->get();

            Log::info('Search updated.', ['search' => $this->search]);
        } else {
            // Si la barre de recherche est vide, ne rien afficher
            $this->users = [];
        }
    }

    // Méthode pour sélectionner un utilisateur
    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;   // Mettre à jour le champ de recherche avec le nom d'utilisateur sélectionné
        $this->users = [];           // Vider la liste des résultats
        Log::info('User selected.', ['user_id' => $userId, 'user_name' => $userName]);
    }


    protected function handlePhotoUpload($projet, $photoField)
    {
        // Vérifier si la propriété est une instance de UploadedFile
        if ($this->$photoField instanceof \Illuminate\Http\UploadedFile) {
            $photo = $this->$photoField;
            $photoName = Carbon::now()->timestamp . '_' . $photoField . '.' . $photo->extension();

            // Redimensionner l'image en la recadrant pour obtenir exactement 500x400 pixels
            $imageResized = Image::make($photo->getRealPath());
            $imageResized->fit(600, 600); // Redimensionner à 500x400 pixels

            // Spécifier le chemin où sauvegarder l'image
            $path = public_path('post/photos/' . $projet->id);

            // Créer le dossier s'il n'existe pas
            if (!file_exists($path)) {
                mkdir($path, 0775, true); // Créer le dossier avec les permissions appropriées
            }

            // Sauvegarder l'image redimensionnée dans le chemin spécifié
            $imageResized->save($path . '/' . $photoName, 90); // 90 est la qualité de l'image

            // Mettre à jour le champ du projet avec le chemin relatif
            $projet->$photoField = 'post/photos/' . $projet->id . '/' . $photoName; // Utiliser le chemin relatif

            // Sauvegarder les modifications dans le projet
            $projet->save();
        }
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

        if ($this->type_financement === 'négocié' && $this->portionActions && !$this->portionObligations) {
            $this->dispatch(
                'formSubmitted',
                ' Le type de financement négocié avec uniquement des actions nest pas autorisé.'
            );
            return; // Stoppe la fonction
        }



        // Calcul du retour sur investissement (montant * taux / 100)
        $tauxInteret = ($this->montant * $this->taux) / 100;

        // Montant total du projet (montant recherché + taux d'intérêt)
        $montantTotal = $this->montant + $tauxInteret;

        // Calcul de la somme allouée aux actions (montant - obligations)
        $actions = (int)$this->montant - (int)$this->portionObligations ?? 0;

        // Calculer le nombre d'actions
        $this->nombreActions = $this->portionActions > 0 ? floor($actions / $this->portionActions) : 0;

        // Indicateur de soumission en cours
        $this->isSubmitting = true;

        try {

            $projet = Projet::create([
                'name' => $this->name,
                'montant' => $this->montant,
                'taux' => $this->taux,
                'description' => $this->description,
                'categorie' => $this->categorie,
                'type_financement' => $this->type_financement,
                'statut' => $this->statut, // Assurez-vous que le statut soit défini ici
                'durer' => $this->durer,
                'date_fin' => $this->durerFin,
                'id_user' => auth()->id(), // ID de l'utilisateur connecté
                'Portion_action' => $this->portionActions,
                'Portion_obligt' => !empty($this->portionObligations) ? (int)$this->portionObligations : NULL,
                'nombreActions' => $this->nombreActions,
                'etat' => 'en cours',
            ]);

            // Gestion des photos en appelant la méthode handlePhotoUpload
            $this->handlePhotoUpload($projet, 'photo1');
            $this->handlePhotoUpload($projet, 'photo2');
            $this->handlePhotoUpload($projet, 'photo3');
            $this->handlePhotoUpload($projet, 'photo4');
            $this->handlePhotoUpload($projet, 'photo5');

            // Sauvegarder les chemins des photos dans le projet
            $projet->save();
            $this->dispatch(
                'formSubmitted',
                'Demande de financement envoyé avec success'
            );

            // Réinitialiser le formulaire et les erreurs
            $this->resetForm();
            $this->resetErrorBag();

            // Récupérer l'ID du projet nouvellement ajouté
            $projetId = $projet->id;

            if ($this->user_id) {
                // Recherche de l'investisseur par user_id
                $investor = Investisseur::where('user_id', $this->user_id)->first();
                // Récupère l'id de l'investisseur
                $investorId = $investor->id ?? null; // ou $investor->id_investisseur selon ton schéma
                Log::info('Investor found.', ['investor_id' => $investorId]);
                // Récupération des informations du projet
                $data = [
                    'id_projet' => $projetId,
                    'montant' => $projet->montant,
                    'duree' => $projet->durer,  // Corriger 'durer' en 'duree'
                    'type_financement' => $projet->type_financement,
                    'user_id' => auth()->id(),
                    'id_investisseur' => $investorId,  // Utilise l'id de l'investisseur récupéré
                ];

                $owner = User::find($this->user_id);

                Notification::send($owner, new DemandeCreditProjetNotification($data));

                $this->successMessage = 'Demande de financement envoyée avec succès.';

                $this->dispatch(
                    'formSubmitted',
                    'Demande de financement envoyé avec success'
                );
            }

            $this->successMessage = 'Demande de financement envoyée avec succès.';
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
        $this->name = '';
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
        $this->bailleur_groupé;
    }

    public function verifyUser()
    {
        // Récupérer l'utilisateur actuellement connecté
        $user = auth()->user();
        $userNumber = $user->phone;

        // Vérifier si le numéro de téléphone de l'utilisateur existe dans la table user_promir
        $userInPromir = UserPromir::where('numero', $userNumber)->first();



        if ($userInPromir) {
            // Vérifier si un score de crédit existe pour cet utilisateur
            $crediScore = CrediScore::where('id_user', $userInPromir->id)->first();

            if ($crediScore) {
                // Vérifier si le score est A+, A, ou A-
                if (in_array($crediScore->ccc, ['A+', 'A', 'A-'])) {

                    $this->message = 'Votre numéro existe dans Promir et votre score de crédit est ' . $crediScore->ccc . ', alors vous êtes éligible au crédit.';
                    $this->dispatch(
                        'formSubmitted',
                        'Votre numéro existe dans Promir et votre score de crédit est ' . $crediScore->ccc . ', Alors vous etes éligible au credit'
                    );
                    $this->isEligible = true;
                } else {

                    $this->dispatch('Votre numéro existe dans Promir, mais votre score de crédit est ' . $crediScore->ccc . ', ce qui n\'est pas éligible.');
                }
            } else {

                $this->dispatch('Votre numéro existe dans Promir, mais aucun score de crédit n\'a été trouvé.');
            }
        } else {
            // L'utilisateur n'existe pas dans user_promir, afficher un message d'erreur
            $this->dispatch('Votre numéro n\'existe pas dans la base de données Promir. Vous n\'êtes pas éligible.');
        }
    }



    // Fonction pour afficher la vue associée
    public function render()
    {
        return view('livewire.add-projet-finance');
    }
}

<?php

namespace App\Livewire;

use App\Models\Projet;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class AddProjetFinance extends Component
{
    public $montant;
    public $taux;
    public $description;
    public $categorie;
    public $type_financement;
    public $statut = 'en attente'; // Par défaut

    public $isSubmitting = false; // Pour l'indicateur de chargement
    public $successMessage = '';  // Message de succès

    protected $rules = [
        'montant' => 'required|numeric',
        'taux' => 'required|numeric',
        'description' => 'required|string',
        'categorie' => 'required|string',
        'type_financement' => 'required|string',
    ];


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
        'type_financement.required' => 'Le type de financement est requis.',
        'type_financement.string' => 'Le type de financement doit être une chaîne de caractères.',
    ];
}

 

    public function submit()
    {
        $this->validate();

        $this->isSubmitting = true;

        // Création du projet avec le statut 'en attente'
        Projet::create([
            'montant' => $this->montant,
            'taux' => $this->taux,
            'description' => $this->description,
            'categorie' => $this->categorie,
            'type_financement' => $this->type_financement,
            'statut' => $this->statut,
            'id_user' => auth()->user()->id, // ID de l'utilisateur connecté
        ]);

        // Réinitialiser le formulaire et afficher le message de succès
        $this->resetForm();
        $this->successMessage = 'Le projet a été ajouté avec succès !';
        $this->isSubmitting = false;
    }

    public function resetForm()
    {
        $this->montant = '';
        $this->taux = '';
        $this->description = '';
        $this->categorie = '';
        $this->type_financement = '';
    }
    public function render()
    {
        return view('livewire.add-projet-finance');
    }
}

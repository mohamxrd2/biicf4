<?php

namespace App\Livewire;

use App\Models\DemandeCredi;
use App\Models\Investisseur;
use App\Models\User;
use App\Notifications\DemandeCreditNotification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Demandecredit extends Component
{
    public $showSection = false;
    public $referenceCode;
    public $price;
    public $creditotal;
    public $duration;
    public $financementType;
    public $username;
    public $bailleur;
    public $startDate;
    public $startTime;
    public $endDate;
    public $endTime;
    public $roi;
    public $quantite;
    public $search = '';   // Champ de recherche
    public $users = [];    // Liste des utilisateurs trouvés
    public $user_id, $sommedemnd, $montantmax,  $quantiteMax, $nameProd, $quantiteMin;

    public $messages = []; 


    protected $listeners = ['userIsEligible' => 'handleEligibility'];

    public function handleEligibility($isEligible, $prix, $montantmax, $quantiteMax, $nameProd, $quantiteMin)
    {
        // Vérifier si l'utilisateur est éligible
        if ($isEligible) {
            // Afficher la section associée
            $this->showSection = true;

            // Mise à jour des propriétés avec les valeurs transmises
            $this->sommedemnd = $prix;           // Prix du produit ou montant demandé
            $this->montantmax = $montantmax;      // Montant maximum de l'investissement ou financement
            $this->quantiteMax = $quantiteMax;    // Quantité maximale disponible
            $this->nameProd = $nameProd;          // Nom du produit
            $this->quantiteMin = $quantiteMin;          // Nom du produit

            // Générer un code de référence aléatoire de 5 chiffres
            $this->referenceCode = $this->generateReferenceCode();
        }
    }



    // Méthode appelée lors de la mise à jour de la recherche
    public function updatedSearch()
    {
        if (!empty($this->search)) {
            // Recherche des utilisateurs dont le nom d'utilisateur correspond à la saisie
            $this->users = User::where('username', 'like', '%' . $this->search . '%')->get();
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
    public function submit()
    {
        try {

            $this->validate([
                'roi' => 'required|numeric',
                'quantite' => 'required|numeric',
                'financementType' => 'required|string',
                'user_id' => 'nullable|exists:investisseurs,user_id', // Assurez-vous que l'utilisateur sélectionné existe
                'bailleur' => 'nullable|string',
                'startDate' => 'required|date',
                'startTime' => 'required|date_format:H:i',
                'endDate' => 'required|date',
                'endTime' => 'required|date_format:H:i',
                'duration' => 'required|numeric',
            ]);
    
    
            // Calculs
            $montantMax = $this->montantmax * (is_nan($this->quantite) ? 0 : $this->quantite);
            $interet = $montantMax * (is_nan($this->roi) ? 0 : $this->roi / 100);
            $creditTotal = $montantMax + $interet;
    
    
    
    
    
    
            // Vérifie si l'investisseur existe
            if ($this->user_id) {
                // Recherche de l'investisseur par user_id
                $investor = Investisseur::where('user_id', $this->user_id)->first();
                // Récupère l'id de l'investisseur
                $investorId = $investor->id ?? null; // ou $investor->id_investisseur selon ton schéma
                Log::info('Investor found.', ['investor_id' => $investorId]);
    
                // Insérer les données dans la table demande_credi
                $demande = DemandeCredi::create([
                    'demande_id' => $this->referenceCode, // Remplacer par la logique appropriée si nécessaire
                    'objet_financement' => 'Demande de crédit pour Achat du produit ' . $this->nameProd,
                    'montant' => $creditTotal,
                    'duree' => $this->duration,
                    'type_financement' => $this->financementType,
                    'bailleur' => $this->bailleur,
                    'id_user' => auth()->id(), // Utilisateur connecté
                    'id_investisseur' => $this->user_id, // Utiliser l'id récupéré
                    'date_debut' => $this->startDate,
                    'heure_debut' => $this->startTime,
                    'date_fin' => $this->endDate,
                    'heure_fin' => $this->endTime,
                    'taux' => $this->roi, // Le taux de retour sur investissement
                ]);
    
                // Optionnel : Ajouter une notification de succès ou rediriger l'utilisateur
                $this->dispatch(
                    'formSubmitted',
                    'Demande de crédit envoyé avec success'
                );
    
                $owner = User::find($this->user_id);
    
                // Envoyer la notification à l'investisseur
                Notification::send($owner, new DemandeCreditNotification($demande));
    
                // Reset des champs après soumission
                $this->reset();
            } else if ($this->bailleur) {
                $investisseurs = Investisseur::where('invest_type', $this->bailleur)
                    ->with('user') // Assure-toi que la relation "user" est définie dans le modèle Investisseur
                    ->get();
    
                // Vérifie s'il y a des investisseurs trouvés
                if ($investisseurs->isEmpty()) {
                    // Gérer le cas où aucun investisseur n'est trouvé
                    Log::warning('Investors not found for bailleur type.', [
                        'bailleur' => $this->bailleur,
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    // Insérer les données dans la table demande_credi pour chaque investisseur
                    foreach ($investisseurs as $investisseur) {
                        $demande = DemandeCredi::create([
                            'demande_id' => $this->referenceCode, // Remplacer par la logique appropriée si nécessaire
                            'objet_financement' => 'Demande de crédit pour Achat du produit ' . $this->nameProd,
                            'montant' => $creditTotal,
                            'duree' => $this->duration,
                            'type_financement' => $this->financementType,
                            'bailleur' => $this->bailleur,
                            'id_user' => auth()->id(), // Utilisateur connecté
                            'id_investisseur' => $investisseur->id, // Utilise l'id de l'investisseur actuel
                            'date_debut' => $this->startDate,
                            'heure_debut' => $this->startTime,
                            'date_fin' => $this->endDate,
                            'heure_fin' => $this->endTime,
                            'taux' => $this->roi, // Le taux de retour sur investissement
                        ]);
                    }
                    // Reset des champs après soumission
                    $this->reset();
                    
                    // Envoi de la notification aux investisseurs concernés
                    foreach ($investisseurs as $investisseur) {
                        // Récupérer l'utilisateur associé à l'investisseur
                        $investisseurUser = $investisseur->user;
    
                        if ($investisseurUser) {
                            Notification::send($investisseurUser, new DemandeCreditNotification($demande));
                        } else {
                            Log::warning('No user found for investor ID: ' . $investisseur->id);
                        }
                    }
                    // Optionnel : Ajouter une notification de succès ou rediriger l'utilisateur
                    $this->dispatch('formSubmitted', 'Demandes de crédit envoyées avec succès');
                }
            }

        }catch (\Illuminate\Validation\ValidationException $e) {
            // Assurez-vous que $messages est un tableau et ajoutez les nouvelles erreurs
            $this->messages = array_merge($this->messages, $e->validator->errors()->all());
        }
       
    }

    // Fonction pour générer un code de référence de 5 chiffres
    private function generateReferenceCode()
    {
        return rand(10000, 99999); // Générer un nombre aléatoire de 5 chiffres
    }

    public function render()
    {
        return view('livewire.demandecredit');
    }
}

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
    public $duration;
    public $financementType;
    public $username;
    public $bailleur;
    public $startDate;
    public $startTime;
    public $endDate;
    public $endTime;
    public $roi;



    protected $listeners = ['userIsEligible' => 'handleEligibility'];

    public function handleEligibility($isEligible)
    {
        // Si l'utilisateur est éligible, afficher la section
        if ($isEligible) {
            $this->showSection = true;

            // Générer un code de référence de 5 chiffres
            $this->referenceCode = $this->generateReferenceCode();
        }
    }
    public $search = '';   // Champ de recherche
    public $users = [];    // Liste des utilisateurs trouvés
    public $user_id;       // ID de l'utilisateur sélectionné

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
        $this->validate([
            'price' => 'required|numeric',
            'duration' => 'required|numeric',
            'financementType' => 'required|string',
            'user_id' => 'required|exists:investisseurs,user_id', // Assurez-vous que l'utilisateur sélectionné existe
            'bailleur' => 'nullable|string',
            'startDate' => 'required|date',
            'startTime' => 'required|date_format:H:i',
            'endDate' => 'required|date',
            'endTime' => 'required|date_format:H:i',
            'roi' => 'required|numeric',
        ]);


        // Recherche de l'investisseur par user_id
        $investor = Investisseur::where('user_id', $this->user_id)->first();

        // Vérifie si l'investisseur existe
        if ($investor) {
            // Récupère l'id de l'investisseur
            $investorId = $investor->id; // ou $investor->id_investisseur selon ton schéma
            Log::info('Investor found.', ['investor_id' => $investorId]);

            // Insérer les données dans la table demande_credi
            $demande = DemandeCredi::create([
                'demande_id' => $this->referenceCode, // Remplacer par la logique appropriée si nécessaire
                'objet_financement' => 'Demande de crédit',
                'montant' => $this->price,
                'duree' => $this->duration,
                'type_financement' => $this->financementType,
                'bailleur' => $this->bailleur,
                'id_user' => auth()->id(), // Utilisateur connecté
                'id_investisseur' => $investorId, // Utiliser l'id récupéré
                'date_debut' => $this->startDate,
                'heure_debut' => $this->startTime,
                'date_fin' => $this->endDate,
                'heure_fin' => $this->endTime,
                'taux' => $this->roi, // Le taux de retour sur investissement
            ]);

            // Optionnel : Ajouter une notification de succès ou rediriger l'utilisateur
            $this->dispatch(
                'formSubmitted',
                'Demandde credit envoyé avec success'
            );

            $owner = User::find($this->user_id);

            // Envoyer la notification à l'investisseur
            Notification::send($owner, new DemandeCreditNotification($demande));

            // Reset des champs après soumission
            $this->reset();
        } else {
            // Si l'investisseur n'existe pas, gérer l'erreur
            Log::warning('Investor not found for user_id.', ['user_id' => $this->user_id]);
            session()->flash('error', 'L\'investisseur sélectionné n\'existe pas.'); // Message d'erreur
        }
        // Reset des champs après soumission
        $this->reset();
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

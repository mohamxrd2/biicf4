<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Admin;
use App\Models\Deposit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Notification;
use App\Notifications\DepositClientNotification;

class DepositClient extends Component
{
    use WithFileUploads;

    public $amount;
    public $receipt;

    public $deposit_type = '';


    public $search = '';
    public $users = [];
    public $user_id;

    public $roi;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'receipt' => 'required|image|max:1024', // Limite la taille du fichier à 1MB
    ];

    public function mount()
    {
        
        $this->resetForm(); 
    }
    public function updatedAmount($value)
    {
        // Calculer le montant avec 10 % en plus, arrondi au multiple de 5 le plus proche
        $this->roi = round(($value * 1.10) / 5) * 5;
    }

    public function submitDeposit()
    {
        try {
            Log::info('Début de la validation des données de dépôt.');

            $this->validate();
            Log::info('Validation réussie.');

            // ID de l'utilisateur authentifié
            $user_id = auth()->id();
            Log::info("Utilisateur authentifié avec l'ID : {$user_id}");

            // Gérer le téléchargement de l'image
            $receiptPath = $this->handlePhotoUpload('receipt');
            Log::info("Image reçue téléchargée et stockée dans le chemin : {$receiptPath}");

            // Créer un nouveau dépôt dans la table Deposit
            $deposit = Deposit::create([
                'montant' => $this->amount,
                'recu' => $receiptPath,
                'user_id' => $user_id,
                'statut' => 'en attente', // Initialiser le statut comme 'en attente'
            ]);

            Log::info("Dépôt enregistré dans la base de données avec succès, ID du dépôt : {$deposit->id}");

            // Message de succès
            session()->flash('message', 'Votre dépôt a été soumis avec succès et est en attente de validation.');

            // Réinitialiser les champs du formulaire
            $this->resetForm(); 
            Log::info("Formulaire réinitialisé avec succès.");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la soumission du dépôt : ' . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de la soumission du dépôt : ' . $e->getMessage());
        }
    }

    public function resetForm()
    {
         $this->deposit_type = '';
         $this->amount = '';
         $this->user_id = '';
         $this->receipt = null;
        
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
   
    protected function handlePhotoUpload($photoField)
    {
        if ($this->$photoField) {
            Log::info("Début du téléchargement pour le champ photo : {$photoField}");

            // Récupérer le fichier photo
            $photo = $this->$photoField;
            $photoName = time() . '.' . $photo->getClientOriginalExtension(); // Générer un nom de fichier unique

            // Définir le chemin où stocker l'image
            $path = public_path('post/'); // Chemin public/post/

            // Vérifier si le dossier 'post' existe, sinon le créer
            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true);
                Log::info("Dossier 'post' créé avec succès.");
            }

            // Tenter de déplacer le fichier
            try {
                $photo->storeAs('post', $photoName, 'public'); // Utiliser le système de stockage Laravel
                Log::info("Photo téléchargée avec succès : {$photoName}");
            } catch (\Exception $e) {
                Log::error("Erreur lors du déplacement de l'image : " . $e->getMessage());
                throw new \Exception("Erreur lors du téléchargement de l'image : " . $e->getMessage());
            }

            // Retourner le chemin de l'image
            return 'post/' . $photoName;
        }

        Log::warning("Aucune photo trouvée pour le champ : {$photoField}");
        return null;
    }

    public function submitSOSRecharge()
    {
        $this->validate([
            'amount' => 'required|numeric|min:1',
        ], [
            'amount.required' => 'Veuillez entrer un montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
        ]);

        session()->flash('message', 'Votre demande a été soumis avec succès et est en attente de validation.');

            // Réinitialiser les champs du formulaire
        $this->resetForm(); 

    }
    

    public function render()
    {
        return view('livewire.deposit-client');
    }
}

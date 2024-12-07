<?php

namespace App\Livewire;

use App\Models\Coi;
use App\Models\User;
use App\Models\Admin;
use App\Models\Deposit;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Investisseur;
use Livewire\WithFileUploads;
use App\Notifications\DepositSos;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
            'roi' => 'required|numeric|min:1',
        ], [
            'amount.required' => 'Veuillez entrer un montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
            'amount.min' => 'Le montant doit être supérieur à 0.',
            'roi.required' => 'Veuillez entrer le ROI.',
            'roi.numeric' => 'Le ROI doit être numérique.',
            'roi.min' => 'Le ROI doit être supérieur à 0.',
        ]);

        // Récupérer l'ID de l'investisseur qui soumet
        $submitterId = Auth::id(); // ou $this->investisseur_id selon ton contexte

        // Récupérer les investisseurs en excluant celui qui soumet
        $cois = Coi::where('Solde', '>=', $this->amount)
            ->with('wallet')
            ->get();

        if ($cois->isEmpty()) {
            session()->flash('error', 'Aucun utilisateur avec un solde suffisant trouvé.');
        } else {
            // Générer un ID unique pour id_sos
            $sosId = Str::uuid()->toString(); // Génère un UUID

            ($codeUnique = $this->generateUniqueReference());
            if (!$codeUnique) {
                Log::error('Code unique non généré.');
                throw new \Exception('Code unique non généré.');
            }

            // Récupérer chaque `user_id` associé aux wallets
            foreach ($cois as $coi) {
                $wallet = $coi->wallet;

                // Vérifier que le wallet est associé à un utilisateur et que cet utilisateur est différent de l'utilisateur qui soumet
                if ($wallet && $wallet->user_id && $wallet->user_id != $submitterId) {
                    $user = User::find($wallet->user_id);

                    if ($user) {
                        // Envoyer la notification
                        $data = [
                            'title' => 'Rechargement SOS',
                            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>
',
                            'description' => 'Vous etes ciblez pour un rechargement SOS par un utlisateur',
                            'code_unique' => $codeUnique,
                            'user_id' => Auth::id(), // Utilisez l'ID de l'utilisateur connecté ici
                            'amount' => $this->amount,
                            'roi' => $this->roi,
                            'id_sos' => $sosId, // Utilisez l'UUID généré ici
                        ];

                        Notification::send($user, new DepositSos($data));
                    }
                }
            }

            session()->flash('message', 'Votre demande a été soumise avec succès et est en attente de validation.');

            // Réinitialiser les champs du formulaire
            $this->resetForm();
        }
    }


    public function resetForm()
    {
        $this->deposit_type = "";
        $this->amount = "";
        $this->user_id = "";
        $this->receipt = null;
        $this->roi = "";
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }


    public function render()
    {
        return view('livewire.deposit-client');
    }
}

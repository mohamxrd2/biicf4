<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Deposit;
use App\Notifications\DepositClientNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class DepositClient extends Component
{
    use WithFileUploads;

    public $amount;
    public $receipt;

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'receipt' => 'required|image|max:1024', // Limite la taille du fichier à 1MB
    ];

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
            $this->reset(['amount', 'receipt']);
            Log::info("Formulaire réinitialisé avec succès.");
        } catch (\Exception $e) {
            Log::error('Erreur lors de la soumission du dépôt : ' . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de la soumission du dépôt : ' . $e->getMessage());
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

    public function render()
    {
        return view('livewire.deposit-client');
    }
}

<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithFileUploads;
use App\Notifications\DepositSend;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\File;

class DepositRecu extends Component
{
    use WithFileUploads;
    public $operator;
    public $phonenumber;
    public $amountDeposit;
    public $roiDeposit;
    public $notification;
    public $userDeposit;
    public $id_sos;
    public $receipt;

    public function mount($id){
        $this->resetForm();
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->roiDeposit = $this->notification->data['roi'];
        $this->amountDeposit = $this->notification->data['amount'];
        $this->userDeposit = User::find($this->notification->data['user_id']);
        $this->id_sos = $this->notification->data['id_sos']?? null;
     
        $this->operator = $this->notification->data['operator'];
        $this->phonenumber = $this->notification->data['phonenumber'];;
     

    }

  
    public function sendRecu()
    {
        $this->validate([
            'receipt' => 'required|image|max:2048', // Limite la taille du fichier à 1MB
        ], [
            'receipt.required' => 'Veuillez sélectionner une photo.',
            'receipt.image' => 'Le fichier doit être une image.',
            'receipt.max' => 'La taille maximale de l\'image est de 1Mo.',
        ]);
        $receiptPath = $this->handlePhotoUpload('receipt');
        Log::info("Image reçue téléchargée et stockée dans le chemin : {$receiptPath}");

        ($codeUnique = $this->generateUniqueReference());
        if (!$codeUnique) {
            Log::error('Code unique non généré.');
            throw new \Exception('Code unique non généré.');
        }


        $data = [
            'title' => 'Recu rechargement SOS',
            'description' => 'Veillez consuter le recu pour le remboursement de votre argent',
            'svg' => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 11.625h4.5m-4.5 2.25h4.5m2.121 1.527c-1.171 1.464-3.07 1.464-4.242 0-1.172-1.465-1.172-3.84 0-5.304 1.171-1.464 3.07-1.464 4.242 0M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>
',
            'code_unique' => $codeUnique,
            'user_id' => Auth::id(),
            'amount' => $this->amountDeposit,
            'roi' => $this->roiDeposit,
            'receipt' => $receiptPath,
        ];


        $owner = User::find($this->userDeposit);

        Notification::send($owner, new DepositSend($data));

        $this->notification->update(['reponse' => 'Envoyée']);

        $this->resetForm(); // Réinitialiser le formulaire

        session()->flash('success', 'Le reçu a été envoyé avec succès.');
    }

    public function resetForm()
    {
        $this->operator = "";
        $this->phonenumber = "";
        $this->receipt = "";
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
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
        return view('livewire.deposit-recu');
    }
}

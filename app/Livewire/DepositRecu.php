<?php

namespace App\Livewire;

use App\Models\User;
use App\Notifications\DepositSend;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class DepositRecu extends Component
{

    public function resetForm()
    {
        $this->operator = "";
        $this->phonenumber = $this->userConnected->phone;
        $this->receipt = "";
    }

    public function sendRecu()
    {
        $this->validate([
            'receipt' => 'required|image|max:1024', // Limite la taille du fichier à 1MB
        ], [
            'receipt.required' => 'Veuillez sélectionner une photo.',
            'receipt.image' => 'Le fichier doit être une image.',
            'receipt.max' => 'La taille maximale de l\'image est de 1Mo.',
        ]);
        $receiptPath = $this->handlePhotoUpload('receipt');
        Log::info("Image reçue téléchargée et stockée dans le chemin : {$receiptPath}");

        $data = [
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
    public function render()
    {
        return view('livewire.deposit-recu');
    }
}

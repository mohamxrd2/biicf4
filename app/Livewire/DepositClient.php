<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;

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
            $this->validate();
    
            // Stockage de l'image du reçu
            $receiptPath = $this->receipt->store('receipts', 'public');
    
            // Logique pour enregistrer les informations de dépôt
    
            // Envoyer une notification ou un email à l'admin pour vérification
    
            // Message de succès
            session()->flash('message', 'Votre dépôt a été soumis avec succès et est en attente de validation.');
    
            // Réinitialiser les champs du formulaire
            $this->reset(['amount', 'receipt']);
        } catch (\Exception $e) {
            // Message d'erreur en cas de problème
            session()->flash('error', 'Une erreur s\'est produite lors de la soumission du dépôt.');
        }
    }

    public function render()
    {
        return view('livewire.deposit-client');
    }
}

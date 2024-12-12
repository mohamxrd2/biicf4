<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\RetraitRib;
use App\Models\Transaction;
use Illuminate\Validation\Rule;

class RetraitShow extends Component
{
    public $retrait;
    public $id;
    public $code1;
    public $code2;
    public $codeRequest1;
    public $codeRequest2;
    public function mount($id)
    {
        $this->id = $id;
        $this->retrait = RetraitRib::with('user')->findOrFail($this->id);
        $this->code1 = $this->retrait->code1 ?? null;
        $this->code2 = $this->retrait->code2 ?? null;
        $this->resetForm();
    }
    public function acceptRetrait()
    {
        if (($this->code1 !== null) || ($this->code2 !== null)) {
            // Validation des données
            $this->validate([
                'codeRequest1' => [
                    'required',
                    'digits:4',
                    Rule::in([$this->code1]), // Vérifie si codeRequest1 est égal à $this->code1
                ],
                'codeRequest2' => [
                    'required',
                    'digits:4',
                    Rule::in([$this->code2]), // Vérifie si codeRequest2 est égal à $this->code2
                ],
            ], [
                'codeRequest1.required' => 'Le code 1 est requis.',
                'codeRequest1.digits' => 'Le code 1 doit être un nombre à 4 chiffres.',
                'codeRequest1.in' => 'Le code 1 est invalide.',
    
                'codeRequest2.required' => 'Le code 2 est requis.',
                'codeRequest2.digits' => 'Le code 2 doit être un nombre à 4 chiffres.',
                'codeRequest2.in' => 'Le code 2 est invalide.',
            ]);
        }

        $retrait = RetraitRib::find($this->id);

        // Création de la transaction pour le retrait

        $referance = $this->generateIntegerReference();

        $this->createTransaction(
             $retrait->id_user,
             $retrait->id_user,
            'Envoie',
            'COC',
            $retrait->amount,
            $referance,
            'Retrait accepté'
        );

        $this->resetForm();


        $retrait->status = 'Accepté';
        $retrait->save();
        session()->flash('message', 'Demande de retrait acceptée');


        
    }

    public function resetForm(){
        $this->codeRequest1 = '';
        $this->codeRequest2 = '';
    }

    public function rejectRetrait()
    {
        $retrait = RetraitRib::find($this->id);

        // Restitution de l'argent gélé

        $userId = $retrait->id_user;
        $userWallet = Wallet::where('user_id', $userId)->first();

        $userWallet->balance += $retrait->amount;
        $userWallet->save();

        // Création de la transaction pour la restitution

        $referance = $this->generateIntegerReference();

        $this->createTransaction(
             $retrait->id_user,
             $retrait->id_user,
            'Réception',
            'COC',
            $retrait->amount,
            $referance,
            'Retrait refusé'
        );
        $this->resetForm();


        $retrait->status = 'Refusé';
        $retrait->save();
        session()->flash('message', 'Demande de retrait refusée');

    }
    protected function createTransaction(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
        $transaction->save();
    }

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
    public function render()
    {
        return view('livewire.retrait-show');
    }
}

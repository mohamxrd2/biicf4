<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;

class RetraitCode extends Component
{
    public $demandeur;
    public $psap;
    public $amount;
    public $notification;
    public $codeRetrait;

    public function mount($id){
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->demandeur = User::find($this->notification->data['userId']);
        $this->psap = User::find($this->notification->data['psap']);
        $this->amount = $this->notification->data['amount'];
        $this->codeRetrait = $this->notification->data['codeRetrait'];
    }
    public function render()
    {
        return view('livewire.retrait-code');
    }
}

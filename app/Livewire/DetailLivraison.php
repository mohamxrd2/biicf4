<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Livraisons;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DetailLivraison extends Component
{
    public $livraison;
    public $id;


    public function mount($id)
    {
        $this->id = $id;
        $this->livraison = Livraisons::with('user')->findOrFail($this->id);
    }

    public function accept()
    {
        $livraison = livraisons::find($this->id);
        $livraison->etat = 'Accepté';
        $livraison->save();

        $user_id = $livraison->user_id;
        $user = User::find($user_id);
        $user->update(['actor_type' => 'livreur']);
    }

    public function refuse()
    {
        $livraison = livraisons::find($this->id);
        $livraison->etat = 'Refusé';
        $livraison->save();
    }

    protected $listeners = ['livraisonUpdated' => '$refresh'];

    public function render()
    {
        return view('livewire.detail-livraison');
    }
}

<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Livraisons;
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

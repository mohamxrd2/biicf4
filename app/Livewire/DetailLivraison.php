<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\livraisons;

class DetailLivraison extends Component
{
    public $livraison;

    public function mount($id)
    {
        $this->livraison = livraisons::with('user')->findOrFail($id);
    }
    public function render()
    {
        return view('livewire.detail-livraison');
    }
}

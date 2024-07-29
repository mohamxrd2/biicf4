<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\livraisons;
use Illuminate\Support\Facades\Auth;

class DetailLivraison extends Component
{
    public $livraison;
    public $id;

    public function mount($id)
    {
        $this->id = $id;
        $this->livraison =livraisons::with('user')->findOrFail($this->id);

    }
    public function render()
    {
        return view('livewire.detail-livraison');
    }
}

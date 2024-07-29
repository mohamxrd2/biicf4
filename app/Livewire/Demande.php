<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\livraisons;

class Demande extends Component
{
    public $livraisons;

    public function mount()
    {
        $this->livraisons = livraisons::with('user')->get();
    }


    public function render()
    {
        return view('livewire.demande');
    }

    
}

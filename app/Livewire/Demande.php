<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Livraisons;

class Demande extends Component
{
    public $livraisons;

    public function mount()
    {
        $this->livraisons = Livraisons::with('user')->get();
    }


    public function render()
    {
        return view('livewire.demande');
    }


}

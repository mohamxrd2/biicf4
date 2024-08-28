<?php

namespace App\Livewire;

use App\Models\Psap;
use Livewire\Component;
use App\Models\Livraisons;

class Demande extends Component
{
    public $livraisons;

    public $psaps;

    

    public function mount()
    {
        $this->livraisons = Livraisons::with('user')->get();

        $this->psaps = Psap::with('user')->get();
    }


    public function render()
    {
        return view('livewire.demande');
    }


}

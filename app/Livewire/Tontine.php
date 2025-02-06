<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Tontine extends Component
{
    #[Layout('biicf.layout.navside')]
    public function render()
    {
        return view('livewire.tontine');
    }
}

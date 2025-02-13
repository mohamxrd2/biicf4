<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

class Tontine extends Component
{
    public $tontineStart;
    public $amount ;
    public $frequency = '';
    public $end_date = '';
    public $potentialGain = 0;


    public function mount()
    {
       $this->tontineStart = true;
    }
    
   
    public function render()
    {
        return view('livewire.tontine');
    }
}

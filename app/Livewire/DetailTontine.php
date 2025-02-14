<?php

namespace App\Livewire;

use Livewire\Component;

class DetailTontine extends Component
{
    public $id;

    public function mount($id) {}
    public function render()
    {
        return view('livewire.detail-tontine');
    }
}

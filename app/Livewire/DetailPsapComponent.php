<?php

namespace App\Livewire;

use App\Models\Psap;
use Livewire\Component;

class DetailPsapComponent extends Component
{
    public $id;

    public $psap;

    public function mount($id)
    {
        $this->id = $id;
        $this->psap = Psap::with('user')->findOrFail($this->id);
    }

    public function accept()
    {
        $psap = Psap::find($this->id);
        $psap->etat = 'Accepté';
        $psap->save();
    }

    public function refuse()
    {
        $psap = Psap::find($this->id);
        $psap->etat = 'Refusé';
        $psap->save();
    }
    public function render()
    {
        return view('livewire.detail-psap-component');
    }
}

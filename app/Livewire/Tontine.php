<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;

class Tontine extends Component
{
    #[Layout('biicf.layout.navside')]

    public function startTontine()
    {
        $tontine = Tontine::create([
            // 'name' => $name,
            // 'amount' => $amount,
            // 'period' => $period,
        ]);

        return response()->json(['message' => 'Tontine créée avec succès!', 'tontine' => $tontine]);
    }
    public function render()
    {
        return view('livewire.tontine');
    }
}

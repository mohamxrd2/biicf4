<?php

namespace App\Livewire;

use App\Models\Consommation;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Consommations extends Component
{
    public function render()
    {
        // Récupérer l'utilisateur connecté via le gardien web
        $user = Auth::guard('web')->user();

        // Récupérer les consommations associées à cet utilisateur
        $consommations = Consommation::where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Compter le nombre de consommations
        $consoCount = $consommations->count();

        // Passer les consommations à la vue
        return view('livewire.consommations', [
            'consommations' => $consommations,
            'consoCount' => $consoCount
        ]);
    }
}

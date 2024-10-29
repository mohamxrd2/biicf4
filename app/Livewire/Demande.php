<?php

namespace App\Livewire;

use App\Models\Psap;
use App\Models\User;
use Livewire\Component;
use App\Models\Livraisons;
use Illuminate\Notifications\DatabaseNotification;

class Demande extends Component
{
    public $livraisons;
    public $psaps;
    public $deposits;

    public function mount()
    {
        $this->livraisons = Livraisons::with('user')->get();
        $this->psaps = Psap::with('user')->get();

        // Récupère les notifications de type DepositClientNotification
        $this->deposits = DatabaseNotification::where('type', 'App\Notifications\DepositClientNotification')
        ->with('notifiable')
        ->latest() // Ajoute cette méthode pour trier par date de création décroissante
        ->get()
        ->map(function ($notification) {
            // Ajoute les informations de l'utilisateur pour chaque notification
            $user = User::find($notification->data['user_id']);
            $notification->user_name = $user ? $user->name : 'Utilisateur inconnu';
            return $notification;
        });
    
    }

    public function render()
    {
        return view('livewire.demande');
    }
}

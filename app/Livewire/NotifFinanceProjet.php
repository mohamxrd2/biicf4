<?php

namespace App\Livewire;

use App\Models\CrediScore;
use App\Models\User;
use App\Models\UserPromir;
use Livewire\Component;

class NotifFinanceProjet extends Component
{
    public $notifications = [];
    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications->filter(function ($notification) {
            // Filtrer les notifications par plusieurs types
            return in_array($notification->type, [
                \App\Notifications\DemandeCreditProjetNotification::class,
            ]);
        });
    }

    public function render()
    {
        $crediScore = null; // Initialiser la variable pour éviter les erreurs
        $userDetails = null; // Initialiser la variable pour la même raison

        // Vérifier s'il existe des notifications à traiter
        foreach ($this->notifications as $notification) {
            // Récupérer l'ID de l'utilisateur depuis les données de la notification
            $userId = $notification->data['user_id'] ?? $notification->data['id_emp'] ?? null;

            if ($userId) {
                // Vérifier si l'utilisateur existe dans la table "users"
                $userDetails = User::find($userId);

                if ($userDetails) {
                    // Récupérer le numéro de téléphone de l'utilisateur
                    $userNumber = $userDetails->phone;

                    // Vérifier si le numéro de téléphone existe dans la table "user_promir"
                    $userInPromir = UserPromir::where('numero', $userNumber)->first();

                    if ($userInPromir) {
                        // Récupérer le score de crédit de l'utilisateur
                        $crediScore = CrediScore::where('id_user', $userInPromir->id)->first();
                    }
                }
            }
        }

        return view('livewire.notif-finance-projet', compact('crediScore', 'userDetails'));
    }
}

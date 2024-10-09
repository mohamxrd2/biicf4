<?php

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;

class NotifFinance extends Component
{

    public $notifications = [];
    public $userDetails;

    public function mount()
    {
        // Récupérer les notifications de l'utilisateur connecté
        $this->notifications = auth()->user()->notifications->filter(function ($notification) {
            return $notification->type === \App\Notifications\DemandeCreditNotification::class;
        });
    }



    public function sendCredit($clientId, $montant)
    {
        // // 1. Récupérer l'utilisateur client
        // $client = User::find($clientId);

        // if (!$client) {
        //     // Si l'utilisateur n'existe pas, retourner une erreur
        //     return response()->json(['error' => 'Client non trouvé'], 404);
        // }

        // // 2. Récupérer le compte en attente du client (Compte des Opérations Courantes ou un autre compte)
        // $compteAttente = Compte::where('user_id', $clientId)->where('type', 'attente')->first();

        // if (!$compteAttente) {
        //     // Si le compte en attente n'existe pas, retourner une erreur
        //     return response()->json(['error' => 'Compte en attente non trouvé'], 404);
        // }

        // // 3. Ajouter le montant envoyé sur le compte en attente
        // $compteAttente->solde += $montant;
        // $compteAttente->save();

        // // 4. Enregistrer la transaction dans la table transactions
        // Transaction::create([
        //     'user_id' => $clientId,
        //     'type' => 'credit',
        //     'montant' => $montant,
        //     'compte_id' => $compteAttente->id,
        //     'status' => 'en attente', // Vous pouvez aussi indiquer "complété" si la transaction est finalisée
        // ]);

        // // 5. Notifier le client (facultatif)
        // $client->notify(new ArgentEnvoyeNotification($montant));

        // return response()->json(['success' => 'Argent envoyé avec succès sur le compte en attente.'], 200);
    }

    public function render()
    {
        return view('livewire.notif-finance');
    }
}

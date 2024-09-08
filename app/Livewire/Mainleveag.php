<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\mainleve;
use App\Notifications\mainlevefour;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Mainleveag extends Component
{
    public $notification;
    public $id;
    public $idProd;
    public $namefourlivr;
    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->idProd = $this->notification->data['idProd'] ?? null;

        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);
    }
    public function mainleve()
    {
        $id_client = Auth::user()->id;
        Log::info('le id du client', ['id_client' => $id_client]);

        $livreur = User::find($this->notification->data['livreur']);
        Log::info('le id du livreur', ['livreur' => $livreur]);

        $fournisseur = User::find($this->notification->data['fournisseur']);
        Log::info('le id du fournisseur', ['fournisseur' => $fournisseur]);

        // Déterminer le prix unitaire
        $prixUnitaire = $this->notification->data['prixProd'] ?? $this->notification->data['prixTrade'];
        Log::info('Prix unitaire déterminé', ['prixUnitaire' => $prixUnitaire]);


        // Vérifier si l'utilisateur est authentifié
        if (!$fournisseur) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

    if($this->notification->data['nameprod'] && $this->notification->data['specificite'] === 'PRO'){
        // Vérifiez si le code_unique existe dans userquantites
        $userQuantites = UserQuantites::where('code_unique', $this->notification->data['code_unique'])->get();
        Log::info('Recherche du code_unique', ['code_unique' => $this->notification->data['code_unique'], 'count' => $userQuantites->count()]);

    }


        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' => $this->notification->data['localité'],
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $id_client,
            'livreur' => $livreur,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd'],
            'date_tot' => $this->notification->data['date_tot'],
            'date_tard' => $this->notification->data['date_tard'],
        ];

        Notification::send($livreur, new mainleve($data));

        Notification::send($fournisseur, new mainlevefour($data));


        $this->notification->update(['reponse' => 'mainleve']);
    }

    public function render()
    {
        return view('livewire.mainleveag');
    }
}

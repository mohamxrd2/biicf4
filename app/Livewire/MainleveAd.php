<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\mainleveclient;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class MainleveAd extends Component
{
    public $namefourlivr;
    public $notification;
    public $id;
    public $idProd;
    public $totalPrice;
    public $produitfat;
    public $dateLivr;
    public $matine;
    public $client;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->idProd = $this->notification->data['idProd'] ?? null;
        $this->namefourlivr = ProduitService::with('user')->find($this->idProd);

        $this->produitfat = ($this->notification->type === 'App\Notifications\AppelOffreGrouperNotification'
        || $this->notification->type === 'App\Notifications\AppelOffreTerminer'
        || $this->notification->type === 'App\Notifications\AppelOffreTerminerGrouper'
        || $this->notification->type === 'App\Notifications\AppelOffre'
        || $this->notification->type === 'App\Notifications\OffreNotifGroup'
        || $this->notification->type === 'App\Notifications\NegosTerminer'
        || $this->notification->type === 'App\Notifications\OffreNegosNotif'
        || $this->notification->type === 'App\Notifications\OffreNegosDone'
        || $this->notification->type === 'App\Notifications\AOGrouper'
        ||  $this->notification->type === 'App\Notifications\OffreNotif'
        || $this->notification->type === 'App\Notifications\Retrait')
        ? null
        : (ProduitService::find($this->notification->data['idProd']) ?? $this->notification->data['produit_id'] ?? null);

        $this->client = User::find($this->notification->data['id_client'] ?? null);

    }

    public function departlivr()
    {

        $id_livreur = Auth::user()->id;

        $this->validate([
            'dateLivr' => 'required|date',
            'matine' => 'required'
        ], [
            'dateLivr.required' => 'La date de livraison est requise.',
            'dateLivr.date' => 'La date de livraison doit être une date valide.',
            'matine.required' => 'La matinée ou soirée est requise.',
        ]);

        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' => $this->localite ?? null,
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $this->notification->data['id_client'],
            'livreur' => $id_livreur,
            'date_livr' => $this->dateLivr,
            'matine' => $this->matine,
            'prixTrade' => $this->notification->data['prixTrade'],
            'prixProd' => $this->notification->data['prixProd']
        ];

        Notification::send($this->client, new mainleveclient($data));

        $this->notification->update(['reponse' => 'mainleveclient']);

        session()->flash('message', 'Livraison marquée comme livrée.');
    }
    public function render()
    {
        return view('livewire.mainleve-ad');
    }
}

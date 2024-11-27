<?php

namespace App\Livewire;

use App\Models\AchatDirect;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\mainleveclient;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class MainleveAd extends Component
{
    public $produit;
    public $notification;
    public $id;
    public $idProd;
    public $totalPrice;
    public $produitfat;
    public $dateLivr;
    public $time;
    public $client;
    public $achatdirect;
    public $fournisseur;
    public $code_verif;
    public $matine; // Ajoutez cette propriété publique


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        $this->produit = ProduitService::with('user')->find($this->achatdirect->idProd);


        $this->fournisseur = User::find($this->achatdirect->userTrader);
        $this->client = User::find($this->achatdirect->userSender);
    }

    public function getCodeVerifProperty()
    {
        // Nettoie le code en enlevant les espaces blancs
        return trim($this->code_verif);
    }
    public function verifyCode()
    {
        // Validation du code de vérification
        $this->validate([
            'code_verif' => 'required|string|size:4', // Taille de 4 caractères
        ], [
            'code_verif.required' => 'Le code de vérification est requis.',
            'code_verif.string' => 'Le code de vérification doit être une chaîne.',
            'code_verif.size' => 'Le code de vérification doit être exactement de 4 caractères.',
        ]);
        if (trim($this->code_verif) === trim($this->notification->data['livreurCode'])) {
            session()->flash('succes', 'Code valide.');
        } else {
            session()->flash('error', 'Code invalide.');
        }

    }
    public function departlivr()
    {

        $this->validate([
            'dateLivr' => 'required|date',
            'time' => 'required'
        ], [
            'dateLivr.required' => 'La date de livraison est requise.',
            'dateLivr.date' => 'La date de livraison doit être une date valide.',
            'time.required' => 'La matinée ou soirée est requise.',
        ]);

        $data = [
            'idProd' => $this->achatdirect->idProd,
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' => $this->notification->data['fournisseur'],
            'livreur' => Auth::id(),
            'date_livr' => $this->dateLivr,
            'time' => $this->time,
            'achat_id' => $this->achatdirect->id,
            'CodeVerification' => random_int(1000, 9999),
            'title' => 'Reception du colis',
            'description' => 'Procéder a la confirmité du colis',
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

<?php

namespace App\Livewire;

use App\Models\AppelOffreUser;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\mainleveclient;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class MainleveAp extends Component
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
    public $appeloffre;
    public $fournisseur;
    public $code_verif;
    public $matine; // Ajoutez cette propriété publique


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appeloffre']);

        $prodUsers = json_decode($this->appeloffre->prodUsers, true); // Décodage en tableau associatif
        $this->produit = ProduitService::where('reference', $this->appeloffre->reference)
            ->where('user_id', $prodUsers)
            ->first();

        $this->fournisseur = User::find($this->produit->user_id);
        $this->client = User::find($this->appeloffre->id_sender);
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
            'code_unique' => $this->appeloffre->code_unique,
            'fournisseur' => $this->notification->data['fournisseur'],
            'livreur' => Auth::id(),
            'prixTrade' => $this->notification->data['prixTrade'],
            'date_livr' => $this->dateLivr,
            'time' => $this->time,
            'id_appeloffre' => $this->appeloffre->id,
            'CodeVerification' => $this->notification->data['livreurCode'],
            'title' => 'Reception du colis',
            'description' => 'Procéder a la confirmité du colis',
        ];

        Notification::send($this->client, new mainleveclient($data));

        $this->notification->update(['reponse' => 'mainleveclient']);

        session()->flash('message', 'Livraison marquée comme livrée.');
    }
    public function render()
    {
        return view('livewire.mainleve-ap');
    }
}

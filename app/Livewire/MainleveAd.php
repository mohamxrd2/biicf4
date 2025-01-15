<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\mainleveAd as NotificationsMainleveAd;
use App\Notifications\mainleveclient;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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
    public $time = '';
    public $client;
    public $achatdirect;
    public $fournisseur;
    public $code_verif;
    public $quantite;
    public $qualite;
    public $diversite;
    public $matine; // Ajoutez cette propriété publique
    public $livreur; // Ajoutez cette propriété publique
    public $user; // Ajoutez cette propriété publique
    public $showMainlever = false;
    public $usersLocations;
    public $nombreFournisseurs;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        $this->produit = ProduitService::with('user')->find($this->achatdirect->idProd);


        $this->fournisseur = User::find($this->notification->data['fournisseur']);
        $this->client = User::find($this->achatdirect->userSender);
        $this->livreur = User::find($this->notification->data['livreur']);
        $this->user = auth()->id();

        if ($this->achatdirect->type_achat === 'OffreGrouper') {
            $this->usersLocations = userquantites::where('code_unique', $this->achatdirect->code_unique)
                ->with('user')  // Eager load user relationship
                ->get();

            $this->nombreFournisseurs = $this->usersLocations->count();
        }
    }

    public function sendNotification($userId)
    {
        $fournisseurCode = $this->notification->data['livreurCode'];

        $dataFournisseur = [
            'code_unique' => $this->achatdirect->code_unique,
            'fournisseurCode' => $fournisseurCode,
            'livreurCode' => $fournisseurCode,
            'livreur' => Auth::id(),
            'fournisseur' => $userId,
            'client' => $this->achatdirect->userSender,
            'achat_id' => $this->achatdirect->id,
            'title' => 'Recuperation de la commande',
            'description' => 'Remettez le colis au livreur.',
        ];

        $user = User::find($userId);
        Notification::send($user, new NotificationsMainleveAd($dataFournisseur));
        event(new NotificationSent($user));
        session()->flash('success', 'Notification envoyée au fournisseur');
    }

    public function toggleComponent()
    {
        $this->showMainlever = true;
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
        try {
            // Validation initiale des réponses
            $responses = [
                'Quantité' => $this->quantite,
                'Qualité' => $this->qualite,
                'Diversité' => $this->diversite,
            ];

            $countYes = count(array_filter($responses, fn($value) => strtolower($value) === 'oui'));

            if ($countYes < 2) {
                session()->flash('error', 'Vous devez sélectionner au moins deux réponses "OUI" pour continuer.');
                return;
            }

            // Validation des champs obligatoires
            $validatedData = $this->validate([
                'dateLivr' => 'required|date',
                'time' => 'required|in:matin,après-midi,soir',
            ], [
                'dateLivr.required' => 'La date de livraison est requise.',
                'dateLivr.date' => 'La date de livraison doit être une date valide.',
                'time.required' => 'La période de livraison est obligatoire.',
                'time.in' => 'Veuillez choisir une période valide : matin, après-midi ou soir.',
            ]);

            // Préparer les données pour la notification
            $data = [
                'code_unique' => $this->achatdirect->code_unique ?? null,
                'fournisseur' => $this->notification->data['fournisseur'] ?? null,
                'livreur' => Auth::id(),
                'prixTrade' => $this->notification->data['prixTrade'] ?? null,
                'date_livr' => $this->dateLivr,
                'time' => $this->time,
                'achat_id' => $this->achatdirect->id ?? null,
                'CodeVerification' => $this->notification->data['livreurCode'] ?? null,
                'title' => 'Réception du colis',
                'description' => 'Procéder à la conformité du colis.',
            ];

            // Vérifications des données critiques
            if (!$data['code_unique'] || !$data['fournisseur'] || !$data['prixTrade']) {
                Log::error('Données manquantes pour la notification de livraison.', $data);
                session()->flash('error', 'Des informations critiques sont manquantes pour finaliser la livraison.');
                return;
            }

            // Envoi de la notification
            Notification::send($this->client, new mainleveclient($data));
            event(new NotificationSent(user: $this->client));

            Log::info('Notification de livraison envoyée avec succès.', $data);

            // Mise à jour de la notification
            $this->notification->update(['reponse' => 'mainleveclient']);

            // Réinitialisation et fermeture du modal
            $this->reset('showMainlever');

            session()->flash('message', 'Livraison marquée comme livrée avec succès.');
        } catch (\Exception $e) {
            // Gestion des exceptions
            Log::error('Erreur lors du traitement de la livraison.', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            session()->flash('error', 'Une erreur est survenue lors du traitement de la livraison. Veuillez réessayer.');
        }
    }

    public function render()
    {
        return view('livewire.mainleve-ad');
    }
}

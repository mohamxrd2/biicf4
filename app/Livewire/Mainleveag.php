<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\User;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\appelivreur;
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

        // Vérifier si le nom du produit et la spécificité sont corrects
        if ($this->notification->data['nameprod'] && $this->notification->data['specificite'] === 'PRO') {
            // Récupérer les enregistrements de la table userquantites avec le code_unique
            $userQuantites = userquantites::where('code_unique', $this->notification->data['nameprod'])->get();

            // Log pour vérifier le nombre d'enregistrements trouvés
            Log::info('Recherche du code_unique', [
                'code_unique' => $this->notification->data['code_unique'],
                'count' => $userQuantites->count()
            ]);

            // ID du premier utilisateur à exclure
            $firstUserId = $userQuantites->first()->user_id;

            // Récupérer les IDs des utilisateurs, en excluant l'utilisateur spécifique
            $userIds = $userQuantites->pluck('user_id')->unique()->reject(function ($id) use ($firstUserId) {
                return $id === $firstUserId; // Exclure le premier utilisateur
            });

            // Récupérer les utilisateurs depuis la base de données
            $users = User::whereIn('id', $userIds)->get();

            // Diviser les utilisateurs en lots de 50 (par exemple)
            $users->chunk(50)->each(function ($userChunk) {
                // Envoyer la notification à chaque lot d'utilisateurs
                Notification::send($userChunk, new Appelivreur());

                // Log pour vérifier le nombre de notifications envoyées pour chaque lot
                Log::info('Notifications envoyées pour un lot', ['user_count' => $userChunk->count()]);
            });
        }


        $data = [
            'idProd' => $this->notification->data['idProd'],
            'code_unique' => $this->notification->data['code_unique'],
            'fournisseur' => $this->notification->data['fournisseur'],
            'localité' => $this->notification->data['localité'],
            'quantite' => $this->notification->data['quantite'],
            'id_client' => $id_client,
            'livreur' => $livreur->id,
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

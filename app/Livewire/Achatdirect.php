<?php

namespace App\Livewire;

use App\Models\Livraisons;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\livraisonAchatdirect;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class Achatdirect extends Component
{
    public $notification;
    public $id;
    public $nombreLivr;
    public $clients;
    public $livreurs;
    public $livreursIds;
    public $livreursCount;
    public $Idsender;
    public $id_sender;
    public $idsender;
    public $modalOpen;

    //ciblage des livreur
    public $clientPays;
    public $clientCommune;
    public $clientContinent;
    public $clientSous_Region;
    public $clientDepartement;



    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->idsender = $this->notification->data['id_sender'] ?? null;
        if (array_key_exists('id_sender', $this->notification->data)) {
            $idSender = $this->notification->data['id_sender'];

            if (is_array($idSender)) {
                //If $idSender is already an array, assign it directly
                $this->id_sender = $idSender;
            } else {
                // If $idSender is a string, use explode to convert it to an array
                $this->id_sender = explode(',', $idSender);
            }
        } else {
            //Handle the case where 'id_sender' does not exist
            $this->id_sender = null; // or any other default value you prefer
        }
        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
    }

    public function ciblageLivreurs()
    {
        // Vérification de l'existence de la clé 'userSender' ou 'id_sender' dans les données de la notification
        $this->Idsender = $this->notification->data['userSender'] ?? $this->notification->data['id_sender'] ?? null;

        if ($this->Idsender === null) {
            return; // Arrêter l'exécution si 'userSender' est null
        }

        $this->clients = []; // Initialiser le tableau de clients

        // Préparer les critères de filtrage pour les livreurs
        $query = Livraisons::query();

        // Vérification si 'id_sender' est un tableau
        if (is_array($this->Idsender)) {
            // Récupérer les informations pour chaque utilisateur dans le tableau
            foreach ($this->Idsender as $id) {
                $client = User::findOrFail($id);

                // Ajouter chaque client à un tableau avec leurs informations en minuscules
                $clientData = [
                    'continent' => strtolower($client->continent),
                    'sous_region' => strtolower($client->sous_region),
                    'country' => strtolower($client->country),
                    'departement' => strtolower($client->departe),
                    'commune' => strtolower($client->commune),
                ];

                $this->clients[] = $clientData;

                // Ajouter des conditions pour chaque client dans la requête
                $query->orWhere(function ($q) use ($clientData) {
                    $q->where(function ($subQuery) use ($clientData) {
                        $subQuery->where('zone', 'proximite')
                            ->whereRaw('LOWER(continent) = ?', [$clientData['continent']])
                            ->whereRaw('LOWER(sous_region) = ?', [$clientData['sous_region']])
                            ->whereRaw('LOWER(pays) = ?', [$clientData['country']])
                            ->whereRaw('LOWER(departe) = ?', [$clientData['departement']])
                            ->whereRaw('LOWER(commune) = ?', [$clientData['commune']]);
                    })
                        ->orWhere(function ($subQuery) use ($clientData) {
                            $subQuery->where('zone', 'locale')
                                ->whereRaw('LOWER(continent) = ?', [$clientData['continent']])
                                ->whereRaw('LOWER(sous_region) = ?', [$clientData['sous_region']])
                                ->whereRaw('LOWER(pays) = ?', [$clientData['country']])
                                ->whereRaw('LOWER(departe) = ?', [$clientData['departement']]);
                        })
                        ->orWhere(function ($subQuery) use ($clientData) {
                            $subQuery->where('zone', 'nationale')
                                ->whereRaw('LOWER(continent) = ?', [$clientData['continent']])
                                ->whereRaw('LOWER(sous_region) = ?', [$clientData['sous_region']]);
                        })
                        ->orWhere(function ($subQuery) use ($clientData) {
                            $subQuery->where('zone', 'sous_regionale')
                                ->whereRaw('LOWER(continent) = ?', [$clientData['continent']]);
                        })
                        ->orWhere(function ($subQuery) {
                            $subQuery->where('zone', 'continentale');
                        });
                });
            }
        } else {
            // Récupérer les informations du client unique
            $client = User::findOrFail($this->Idsender);
            $this->clientContinent = strtolower($client->continent);
            $this->clientSous_Region = strtolower($client->sous_region);
            $this->clientPays = strtolower($client->country);
            $this->clientDepartement = strtolower($client->departe);
            $this->clientCommune = strtolower($client->commune);

            // Ajouter les conditions de filtrage pour un client unique
            $query->where(function ($q) {
                $q->where(function ($subQuery) {
                    $subQuery->where('zone', 'proximite')
                        ->whereRaw('LOWER(continent) = ?', [$this->clientContinent])
                        ->whereRaw('LOWER(sous_region) = ?', [$this->clientSous_Region])
                        ->whereRaw('LOWER(pays) = ?', [$this->clientPays])
                        ->whereRaw('LOWER(departe) = ?', [$this->clientDepartement])
                        ->whereRaw('LOWER(commune) = ?', [$this->clientCommune]);
                })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('zone', 'locale')
                            ->whereRaw('LOWER(continent) = ?', [$this->clientContinent])
                            ->whereRaw('LOWER(sous_region) = ?', [$this->clientSous_Region])
                            ->whereRaw('LOWER(pays) = ?', [$this->clientPays])
                            ->whereRaw('LOWER(departe) = ?', [$this->clientDepartement]);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('zone', 'nationale')
                            ->whereRaw('LOWER(continent) = ?', [$this->clientContinent])
                            ->whereRaw('LOWER(sous_region) = ?', [$this->clientSous_Region]);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('zone', 'sous_regionale')
                            ->whereRaw('LOWER(continent) = ?', [$this->clientContinent]);
                    })
                    ->orWhere(function ($subQuery) {
                        $subQuery->where('zone', 'continentale');
                    });
            });
        }

        // Récupérer les livreurs éligibles
        $this->livreurs = $query->where('etat', 'Accepté')->get();

        // Extraire les IDs des livreurs éligibles
        $this->livreursIds = $this->livreurs->pluck('user_id');
        $this->livreursCount = $this->livreurs->count();
    }

    public function accepter($textareaContent = null)
    {
        // Récupérez le contenu du textarea depuis la requête
        $textareaContent = $textareaContent ?? '';

        // Vérifiez si l'utilisateur a un portefeuille
        $userId = Auth::id();
        $userWallet = Wallet::where('user_id', $userId)->first();
        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        // Mettez à jour la notification
        $notification = NotificationEd::find($this->notification->id);
        if (!$notification) {
            session()->flash('error', 'Notification introuvable.');
            return;
        }
        $notification->reponse = 'accepte';
        $notification->save();

        // Calculez le montant total et le code unique
        $requiredAmount = $this->notification->data['montantTotal'];
        $pourcentSomme = $requiredAmount * 0.1;
        $totalSom = $requiredAmount - $pourcentSomme;

        $code_livr = $this->code_unique ?? $this->genererCodeAleatoire(10);
        $produit = ProduitService::find($this->notification->data['idProd'] ?? $this->idProd2);

        // Préparez les données pour la notification
        $data = [
            'idProd' => $this->notification->data['idProd'] ?? $this->idProd2,
            'id_trader' =>  $this->notification->data['userTrader'] ?? null,
            'totalSom' => $requiredAmount ?? null,
            'quantite' => $this->notification->data['quantité'] ?? null,
            'localite' => $this->notification->data['localite'] ?? null,
            'userSender' => $this->notification->data['userSender'] ?? $this->notification->data['id_sender'] ?? null,
            'code_livr' => $code_livr,
            'prixProd' => $this->notification->data['prixTrade'] ?? $produit->prix ?? null,
            'textareaContent' => $textareaContent
        ];
        Log::info('data', ['data' => $data]);


        // Si aucune quantité utilisateur trouvée, envoyez des notifications aux livreurs seulement
        if (!empty($this->livreursIds)) {
            foreach ($this->livreursIds as $livreurId) {
                $livreur = User::find($livreurId);
                if ($livreur) {
                    Notification::send($livreur, new livraisonAchatdirect($data));
                    // Log l'envoi de la notification
                    Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
                } else {
                    // Log un avertissement si aucun livreur trouvé
                    Log::warning('Livreur non trouvé pour l\'ID', ['livreur_id' => $livreurId]);
                }
            }
        }


        session()->flash('success', 'Achat accepté.');

        $this->modalOpen = false;
        $this->notification->update(['reponse' => 'accepte']);
    }
    private function genererCodeAleatoire($longueur)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $code = '';

        for ($i = 0; $i < $longueur; $i++) {
            $code .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return $code;
    }
    public function render()
    {
        return view('livewire.Achatdirect');
    }
}

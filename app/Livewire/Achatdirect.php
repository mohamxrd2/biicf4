<?php

namespace App\Livewire;

use App\Models\Livraisons;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\livraisonAchatdirect;
use Carbon\Carbon;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;
use Livewire\Component;

class Achatdirect extends Component
{
    use WithFileUploads;

    public $notification;
    public $id;
    public $nombreLivr;
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
    public $photoProd1;
    public $textareaValue;



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
        $this->Idsender = $this->notification->data['userSender'] ?? null;

        if ($this->Idsender === null) {
            return; // Arrêter l'exécution si 'userSender' est null
        }



        // Préparer les critères de filtrage pour les livreurs
        $query = Livraisons::query();

        // Vérification si 'id_sender' est un tableau

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


        // Récupérer les livreurs éligibles
        $this->livreurs = $query->where('etat', 'Accepté')->get();

        // Extraire les IDs des livreurs éligibles
        $this->livreursIds = $this->livreurs->pluck('user_id');
        $this->livreursCount = $this->livreurs->count();
    }

    public function accepter()
    {
        $validated = $this->validate([
            'photoProd1' => 'required|image|max:1024', // Limite de taille du fichier à 1 MB
            'textareaValue' => 'required', // Limite de taille du fichier à 1 MB
        ]);


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

        // Téléchargez la photo et obtenez le nom de fichier
        $photoName = $this->handlePhotoUpload('photoProd1');

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
            'textareaContent' => $validated['textareaValue'], // Correction ici
            'photoProd1' =>$photoName ,
            'dateTot' => null,
            'dateTard' => null,
        ];
        // dd($data );
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

    protected function handlePhotoUpload($photoField)
    {
        if ($this->$photoField instanceof \Illuminate\Http\UploadedFile) {
            $photo = $this->$photoField;
            $photoName = Carbon::now()->timestamp . '_' . $photoField . '.' . $photo->extension();

            // Redimensionner l'image
            $imageResized = Image::make($photo->getRealPath())->fit(500, 400);

            // Sauvegarder l'image
            $imageResized->save(public_path('post/all/' . $photoName), 90);

            return $photoName; // Retourne le nom du fichier pour mise à jour ultérieure
        }
        return null; // Retourne null si aucun fichier valide
    }

    public function takeaway()
    {
        // Log pour vérifier que la méthode est appelée
        Log::info('Méthode takeaway appelée.', ['notification' => $this->notification]);

        // Extraire les données correctement
        $notificationData = $this->notification->data;


        // Si la référence et l'ID du trader sont manquants, essayer de trouver le produit par ID direct
        $produit = ProduitService::find($notificationData['idProd'] ?? null);

        // Vérifier si le produit existe
        if ($produit) {
            $prixProd = $produit->prix;
        } else {
            Log::error('Produit non trouvé.', ['idProd' => $notificationData['idProd']]);
            session()->flash('error', 'Produit non trouvé.');
            return;
        }

        // À partir d'ici, tu peux utiliser $prixProd pour les étapes suivantes
        $code_livr = isset($this->code_unique) ? $this->code_unique : $this->genererCodeAleatoire(10);

        $details = [
            'code_unique' => $code_livr,
            'fournisseur' => $notificationData['userTrader'] ?? null, // Correction: Utiliser 'id_trader'
            'idProd' => $notificationData['idProd'] ?? null,
            'quantiteC' => $this->notification->data['quantité'] ?? null, // Correction: Utiliser 'quantite'
            'prixProd' => $prixProd ?? null,
        ];

        // Log pour vérifier les détails avant l'envoi de la notification
        Log::info('Détails de la notification préparés.', ['details' => $details]);

        // Vérifiez si 'userSender' est présent, sinon utilisez 'id_sender'
        $userSenderId = $notificationData['userSender'] ?? null;

        if ($userSenderId) {
            $userSender = User::find($userSenderId);
            if ($userSender) {
                Log::info('Utilisateur expéditeur trouvé.', ['userSenderId' => $userSender->id]);

                // Envoi de la notification
                Notification::send($userSender, new CountdownNotificationAd($details));
                Log::info('Notification envoyée avec succès.', ['userSenderId' => $userSender->id, 'details' => $details]);

                // Récupérez la notification pour mise à jour
                $notification = $userSender->notifications()->where('type', CountdownNotificationAd::class)->latest()->first();

                if ($notification) {
                    // Mettez à jour le champ 'type_achat' dans la notification
                    $notification->update(['type_achat' => 'reserv/take']);
                }
            } else {
                Log::error('Utilisateur expéditeur non trouvé.', ['userSenderId' => $userSenderId]);
                session()->flash('error', 'Utilisateur expéditeur non trouvé.');
                return;
            }
        } else {
            Log::error('Détails de notification non valides.', ['notification' => $this->notification]);
            session()->flash('error', 'Détails de notification non valides.');
            return;
        }

        // Mettre à jour la notification originale
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

<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect as ModelsAchatDirect;
use App\Models\Countdown;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Wallet;
use Livewire\Component;
use App\Models\Livraisons;
use App\Models\Transaction;
use Livewire\WithFileUploads;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use App\Notifications\RefusAchat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Notifications\livraisonAchatdirect;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CountdownNotificationAd;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;

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
    public $photoProd;
    public $textareaValue;
    public $produits;
    public $achatdirect;
    public $prixFin;
    public $time;
    public $error;
    public $timestamp;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;
    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructe
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function mount($id)
    {

        $this->timeServer();
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->produits = ProduitService::find($this->notification->data['idProd']);
        $this->achatdirect = ModelsAchatDirect::find($this->notification->data['achat_id']);
        $this->prixFin = $this->achatdirect->montantTotal - $this->achatdirect->montantTotal * 0.01;


        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
    }

    public function timeServer()
    {
        // Faire plusieurs tentatives de récupération pour plus de précision
        $attempts = 3;
        $times = [];

        for ($i = 0; $i < $attempts; $i++) {
            // Récupération de l'heure via le service
            $currentTime = $this->recuperationTimer->getTime();
            if ($currentTime) {
                $times[] = $currentTime;
            }
            // Petit délai entre chaque tentative
            usleep(50000); // 50ms
        }

        if (empty($times)) {
            // Si aucune tentative n'a réussi, utiliser l'heure système
            $this->error = "Impossible de synchroniser l'heure. Utilisation de l'heure système.";
            $this->time = now()->timestamp * 1000;
        } else {
            // Utiliser la médiane des temps récupérés pour plus de précision
            sort($times);
            $medianIndex = floor(count($times) / 2);
            $this->time = $times[$medianIndex];
            $this->error = null;
        }

        // Convertir en secondes
        $seconds = intval($this->time / 1000);
        // Créer un objet Carbon pour le timestamp
        $this->timestamp = Carbon::createFromTimestamp($seconds);

        // Log pour debug
        Log::info('Timer actualisé', [
            'timestamp' => $this->timestamp,
            'time_ms' => $this->time,
            'attempts' => count($times)
        ]);
    }
    public function ciblageLivreurs()
    {

        // Vérification de l'existence de 'userSender' dans les données de la notification
        $this->Idsender = $this->achatdirect->userSender ?? null;

        if (!$this->Idsender) {
            session()->flash('error', 'L\'expéditeur n\'est pas défini.');
            return;
        }

        // Récupérer les informations du client
        $client = User::find($this->Idsender);
        if (!$client) {
            session()->flash('error', 'Client introuvable.');
            return;
        }

        // Normalisation des données du client pour comparaison
        $this->clientContinent = strtolower($client->continent);
        $this->clientSous_Region = strtolower($client->sous_region);
        $this->clientPays = strtolower($client->country);
        $this->clientDepartement = strtolower($client->departe);
        $this->clientCommune = strtolower($client->commune);

        // Préparer les critères de filtrage pour les livreurs
        $query = Livraisons::query();

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

        // Filtrer les livreurs acceptés
        $this->livreurs = $query->where('etat', 'Accepté')->get();

        // Extraire les IDs et compter les livreurs
        $this->livreursIds = $this->livreurs->pluck('user_id');
        $this->livreursCount = $this->livreurs->count();
    }

    public function accepter()
    {
        $this->timeServer();

        $validated = $this->validate([
            'photoProd' => 'required|image|max:1024', // Limite à 1 MB
            'textareaValue' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si l'utilisateur a un portefeuille
            $userId = Auth::id();
            $userWallet = Wallet::where('user_id', $userId)->first();
            if (!$userWallet) {
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }

            // Téléchargez la photo
            $photoName = $this->handlePhotoUpload('photoProd');

            // Préparer les données pour la notification
            $data = [
                'idProd' => $this->notification->data['idProd'] ?? null,
                'code_livr' => $this->notification->data['code_unique'],
                'textareaContent' => $validated['textareaValue'],
                'photoProd' => $photoName,
                'achat_id' => $this->achatdirect->id,
            ];

            if (!$data['idProd']) {
                throw new Exception('Identifiant du produit introuvable.');
            }

            $this->startCountdown($data['code_livr']);

            // Envoyer les notifications aux livreurs
            if ($this->livreursIds->isNotEmpty()) {
                foreach ($this->livreursIds as $livreurId) {
                    $livreur = User::find($livreurId);
                    if ($livreur) {
                        Notification::send($livreur, new livraisonAchatdirect($data));
                        event(new NotificationSent($livreur));

                        // Log l'envoi de la notification
                        Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
                    }
                }
            }



            // Mettre à jour la notification
            $this->notification->update(['reponse' => 'accepte']);

            DB::commit();

            // Retourner une confirmation
            $this->dispatch('formSubmitted', 'Commande acceptée avec succès. Notifications envoyées aux livreurs.');
            $this->modalOpen = false;
        } catch (Exception $e) {
            // Annuler la transaction et gérer l'erreur
            DB::rollBack();
            session()->flash('error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }
    }
    public function startCountdown($code_livr)
    {
        // Utiliser firstOrCreate avec des conditions plus spécifiques
        $countdown = Countdown::firstOrCreate(
            [
                'code_unique' => $code_livr,
                'is_active' => true
            ],
            [
                'user_id' => Auth::id(),
                'userSender' => $this->achatdirect->userSender,
                'start_time' => $this->timestamp,
                'difference' => 'ad',
                'id_achat' => $this->achatdirect->id,
                'time_remaining' => 120,
                'end_time' => $this->timestamp->addMinutes(2),
            ]
        );
        if ($countdown->wasRecentlyCreated) {
            $this->countdownId = $countdown->id;
            $this->isRunning = true;
            $this->timeRemaining = 120;
            // Dispatch le job immédiatement
            dispatch(new ProcessCountdown($countdown->id, $code_livr))
                ->onQueue('default')
                ->afterCommit();
            event(new CountdownStarted(120, $code_livr));
            Log::info('Countdown started', [
                'countdown_id' => $countdown->id,
                'code_livr' => $code_livr
            ]);
        }
    }

    public function refuser()
    {
        $userId = Auth::id();
        $clientId = $this->achatdirect->userSender;
        $montantTotal = $this->achatdirect->montantTotal;

        DB::beginTransaction();
        try {
            // Vérifier l'existence du portefeuille de l'utilisateur
            $userWallet = Wallet::where('user_id', $clientId)->firstOrFail();

            // Ajouter le montant au portefeuille du client
            $userWallet->increment('balance', $montantTotal);

            // Générer une référence unique
            $reference_id = $this->generateIntegerReference();

            // Créer une transaction
            $this->createTransaction(
                $userId,
                $clientId,
                'Réception',
                $montantTotal,
                $reference_id,
                'Restitution de fonds(achat refusé)',
                'effectué',
                'COC'
            );

            $data = [
                'id' => $this->achatdirect->id,
                'idProd' => $this->notification->data['idProd'] ?? null,
                'code_unique' => $this->notification->data['code_unique'],
                'title' => 'Facture Refusée',
                'description' => 'Le fournisseur a refuser votre commande.',
            ];

            // Envoyer une notification au client
            $owner = User::findOrFail($clientId);
            Notification::send($owner, new RefusAchat($data));
            // Déclencher un événement pour signaler l'envoi de la notification
            event(new NotificationSent($owner));
            // Mettre à jour la réponse de la notification
            $this->notification->update(['reponse' => 'refuser']);

            // Valider la transaction
            DB::commit();

            session()->flash('success', 'Achat refusé avec succès.');
        } catch (Exception $e) {
            // Annuler la transaction si un élément est introuvable
            DB::rollBack();
            session()->flash('error', 'Un élément requis est introuvable : ' . $e->getMessage());
        }
    }

    public function takeaway()
    {
        DB::beginTransaction();

        try {
            // Vérifiez que notification et achatdirect sont définis
            if (!$this->notification || !$this->achatdirect) {
                Log::error('Notification ou achatdirect non défini.', [
                    'notification' => $this->notification,
                    'achatdirect' => $this->achatdirect,
                ]);
                session()->flash('error', 'Données manquantes pour traiter la demande.');
                return;
            }

            // Préparer les détails pour la notification
            $details = [
                'prixFin' =>  $this->prixFin ?? null,
                'code_unique' => $this->achatdirect->code_unique ?? null,
                'id' => $this->achatdirect->id ?? null,
            ];

            // Trouvez l'utilisateur expéditeur
            $userSender = User::find($this->achatdirect->userSender);

            // Envoi de la notification
            Notification::send($userSender, new CountdownNotificationAd($details));
            event(new NotificationSent($userSender));

            // Récupérer la dernière notification de type AppelOffreTerminer
            $notification = $userSender->notifications()
                ->where('type', CountdownNotificationAd::class)
                ->latest() // Prend la dernière notification
                ->first();
            if ($notification) {
                // Mise à jour de la notification existante
                $notification->update(['type_achat' => 'Take Away']);
                Log::info('Mise à jour de la notification existante.', ['notification_id' => $notification->id]);
            } else {
                Log::warning('Aucune notification de type AppelOffreTerminer trouvée.', ['userSenderId' => $userSender->id]);
            }

            // Mettre à jour la notification originale
            $this->notification->update(['reponse' => 'accepte', 'type_achat' => 'Take Away']);
            Log::info('Notification originale mise à jour avec succès.', [
                'notificationId' => $this->notification->id,
            ]);
            // Après l'envoi de la notification
            event(new NotificationSent($userSender));
            // Valider la transaction
            DB::commit();
        } catch (Exception $e) {
            // Annuler la transaction si un élément est introuvable
            DB::rollBack();
            session()->flash('error', 'Un élément requis est introuvable : ' . $e->getMessage());
        }
    }

    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status,  string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->type_compte = $type_compte;
        $transaction->status = $status;
        $transaction->save();
    }
    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
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

    public function render()
    {
        return view('livewire.Achatdirect');
    }
}

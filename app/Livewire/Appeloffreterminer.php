<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use App\Models\Countdown;
use App\Services\TimeSync\TimeSyncService;
use Livewire\Component;
use App\Models\Livraisons;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\CountdownNotificationAd;
use App\Notifications\livraisonAchatdirect;
use App\Notifications\RefusAchat;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Exception;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\WithFileUploads;

class Appeloffreterminer extends Component
{
    use WithFileUploads;

    public $notification;
    public $id;
    public $nombreLivr;
    public $clients;
    public $livreurs;
    public $livreursIds;
    public $livreursCount;
    public $modalOpen;
    public $Idsender;
    public $prixFin;
    public $prixTotal;
    public $appeloffre;
    public $produit;

    //ciblage des livreur
    public $clientPays;
    public $clientCommune;
    public $clientContinent;
    public $clientSous_Region;
    public $clientDepartement;
    public $produitService;
    public $photoProd;
    public $textareaValue;
    public $timestamp;
    public $time;
    public $error;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;

    protected $recuperationTimer;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }



    public function mount($id)
    {
        $this->timeServer();

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appeloffre']);

        $this->produit = ProduitService::where('reference', $this->appeloffre->reference)
            ->where('user_id', $this->notification->data['id_trader'])
            ->first();
        $this->prixTotal = $this->notification->data['prixTrade'] * $this->appeloffre->quantity;
        $this->prixFin = $this->prixTotal - $this->prixTotal * 0.01;


        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
    }
    public function ciblageLivreurs()
    {
        // Vérification de l'existence de 'userSender' dans les données de la notification
        $this->Idsender = $this->appeloffre->id_sender ?? null;

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

        // Validation des entrées
        $validated = $this->validate([
            'photoProd' => 'required|image|max:1024', // Limite à 1 MB
            'textareaValue' => 'required',
        ]);

        $userId = Auth::id(); // Obtenir l'ID de l'utilisateur connecté

        // Vérifier si l'utilisateur est authentifié
        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        DB::beginTransaction(); // Démarrer une transaction
        try {

            // Télécharger la photo et gérer le fichier
            $photoName = $this->handlePhotoUpload('photoProd');

            // Mettre à jour AppelOffreUser
            $appelOffreUser = AppelOffreUser::find($this->appeloffre->id);
            if (!$appelOffreUser) {
                throw new Exception('Appel d\'offre introuvable.');
            }

            $appelOffreUser->update([
                'prodUser' => array_merge($appelOffreUser->prodUser ?? [], [$userId]), // Ajouter l'utilisateur au tableau
                'montant_total' => $this->prixTotal,
                'lowestPricedProduct' => $this->notification->data['prixTrade'],
            ]);

            // Enregistrer l'achat dans la table AchatDirectModel
            $achatdirect = AchatDirect::create([
                'photoProd' => $photoName,  // Quantité récupérée de userquantites
                'prix' => $this->notification->data['prixTrade'],
                'nameProd' => $this->produit->name,  // Quantité récupérée de userquantites
                'quantité' => $this->appeloffre->quantity,  // Quantité récupérée de userquantites
                'montantTotal' => $this->prixTotal,
                'type_achat' => 'appelOffre',
                'localite' => $this->appeloffre->localite,
                'date_tot' => $this->appeloffre->date_tot,
                'date_tard' => $this->appeloffre->date_tard,
                'userTrader' => Auth::id(),
                'userSender' => $this->appeloffre->id_sender,  // Utilisateur qui a saisi l'achat
                'idProd' => $this->produit->id,
                'code_unique' => $this->appeloffre->code_unique,
            ]);

            // Préparer les données pour la notification
            $data = [
                'idProd' => $this->produit->id,
                'code_livr' => $this->generateUniqueReference(),
                'textareaContent' => $validated['textareaValue'],
                'photoProd' => $photoName,
                'achat_id' => $achatdirect->id ?? null,
                'title' => 'Negociations des livreurs',
                'description' => 'Cliquez pour particicper a la negociation',

            ];
            $userSender = $achatdirect->userSender;
            $id = $achatdirect->id;
            $this->startCountdown($data['code_livr'], $userSender, $id);


            // Vérifier l'existence de l'identifiant du produit
            if (!$data['idProd']) {
                throw new Exception('Identifiant du produit introuvable.');
            }

            // Envoyer des notifications aux livreurs si disponibles
            if (!empty($this->livreursIds)) {
                foreach ($this->livreursIds as $livreurId) {
                    $livreur = User::find($livreurId);
                    if ($livreur) {
                        Notification::send($livreur, new livraisonAchatdirect($data));

                        event(new NotificationSent($livreur)); // Lancer l'événement
                    }
                }
            }

            // Mettre à jour la notification
            $this->notification->update(['reponse' => 'accepte']);

            DB::commit(); // Confirmer la transaction

            // Retourner une confirmation
            $this->dispatch('formSubmitted', 'Commande acceptée avec succès. Notifications envoyées aux livreurs.');
            $this->modalOpen = false;
        } catch (Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            Log::error('Erreur lors de l\'acceptation de la commande', ['message' => $e->getMessage()]);
            session()->flash('error', 'Une erreur s\'est produite : ' . $e->getMessage());
        }
    }

    public function startCountdown($code_livr, $userSender, $id)
    {
        // Utiliser firstOrCreate avec des conditions plus spécifiques
        $countdown = Countdown::firstOrCreate(
            [
                'code_unique' => $code_livr,
                'is_active' => true
            ],
            [
                'user_id' => Auth::id(),
                'userSender' => $userSender,
                'start_time' => $this->timestamp,
                'difference' => 'ad',
                'id_achat' => $id,
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

    public function timeServer()
    {
        $timeSync = new TimeSyncService($this->recuperationTimer);
        $result = $timeSync->getSynchronizedTime();
        $this->time = $result['time'];
        $this->error = $result['error'];
        $this->timestamp = $result['timestamp'];
    }
    public function refuser()
    {
        $userId = Auth::id();
        $clientId = $this->appeloffre->id_sender;
        $montantTotal = $this->prixTotal;

        DB::beginTransaction();

        try {
            // Vérifier l'existence du portefeuille de l'utilisateur
            $userWallet = Wallet::where('user_id', $clientId)->firstOrFail();


            // Ajouter le montant au portefeuille du client
            $userWallet->increment('balance', $montantTotal);

            // Générer une référence unique
            $reference_id = $this->generateIntegerReference();


            // Enregistrer l'achat dans la table AchatDirectModel
            $achatdirect = AchatDirect::create([
                'nameProd' => $this->produit->name,  // Quantité récupérée de userquantites
                'quantité' => $this->appeloffre->quantity,  // Quantité récupérée de userquantites
                'montantTotal' => $this->prixTotal,
                'localite' => $this->appeloffre->localite,
                'date_tot' => $this->appeloffre->date_tot,
                'date_tard' => $this->appeloffre->date_tard,
                'userTrader' => Auth::id(),
                'userSender' => $this->appeloffre->id_sender,  // Utilisateur qui a saisi l'achat
                'idProd' => $this->produit->id,
                'code_unique' => $this->appeloffre->code_unique,
            ]);

            // Envoyer une notification au client
            $owner = User::findOrFail($clientId);
            Notification::send($owner, new RefusAchat($this->notification->data['code_unique']));
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
            // Vérifiez que notification et appeloffre sont définis
            if (!$this->notification || !$this->appeloffre) {
                Log::error('Notification ou appeloffre non défini.', [
                    'notification' => $this->notification,
                    'appeloffre' => $this->appeloffre,
                ]);
                session()->flash('error', 'Données manquantes pour traiter la demande.');
                return;
            }
            // Enregistrer l'achat dans la table AchatDirectModel
            $achatdirect = AchatDirect::create([
                'nameProd' => $this->produit->name,  // Quantité récupérée de userquantites
                'quantité' => $this->appeloffre->quantity,  // Quantité récupérée de userquantites
                'montantTotal' => $this->prixTotal,
                'localite' => $this->appeloffre->localite,
                'date_tot' => $this->appeloffre->date_tot,
                'date_tard' => $this->appeloffre->date_tard,
                'userTrader' => Auth::id(),
                'userSender' => $this->appeloffre->id_sender,  // Utilisateur qui a saisi l'achat
                'idProd' => $this->produit->id,
                'code_unique' => $this->appeloffre->code_unique,
            ]);

            // Préparer les détails pour la notification
            $details = [
                'prixFin' =>  $this->prixFin ?? null,
                'code_unique' => $this->notification->data['code_unique'] ?? null,
                'id' => $achatdirect->id ?? null,
            ];

            // Trouvez l'utilisateur expéditeur
            $userSender = User::find($this->appeloffre->id_sender);


            // Envoi de la notification
            Notification::send($userSender, new CountdownNotificationAd($details));
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
        $timestamp = $this->appeloffre->date_->getTimestamp() * 1000 + $this->appeloffre->date_->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
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
        return view('livewire.appeloffreterminer');
    }
}

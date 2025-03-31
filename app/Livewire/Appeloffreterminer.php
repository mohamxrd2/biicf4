<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use App\Models\Countdown;
use App\Services\AppelOffreService;
use App\Services\generateUniqueReference;
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
use App\Services\LivreurCibleService;
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
    public $id, $nombreLivr, $clients, $livreurs, $livreursIds;
    public $livreursCount, $modalOpen, $Idsender, $prixFin, $prixTotal, $appeloffre, $produit;

    //ciblage des livreur
    public $clientPays, $clientCommune, $clientContinent, $clientSous_Region, $clientDepartement;
    public $produitService, $photoProd, $textareaValue, $timestamp, $time, $error, $countdownId,
        $isRunning, $timeRemaining, $isProcessing = false;

    protected $recuperationTimer;
    protected $appelOffreService;
    protected $livreurCibleService;

    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
        $this->livreurCibleService = new LivreurCibleService();
    }

    public function boot(AppelOffreService $appelOffreService)
    {
        $this->appelOffreService = $appelOffreService;
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
        $this->prixFin = $this->prixTotal - $this->prixTotal * 0.1;


        // Cibler les livreurs pour cet appel d'offre
        $resultatCiblage = $this->livreurCibleService->targeterLivreurs($this->appeloffre->id_sender);

        if ($resultatCiblage) {
            // Faire quelque chose avec les livreurs ciblés
            $this->livreurs = $resultatCiblage['livreurs'];
            $this->livreursIds = $resultatCiblage['livreurs_ids'];
            $this->livreursCount = count($this->livreurs);
        }

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
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
                'data_finance' => json_encode([
                    'prix_negociation' => $this->notification->data['prixTrade'],
                    'montantTotal' => $this->prixTotal,
                    'quantité' => $this->appeloffre->quantity,
                    'prix_apres_comission' => $this->prixFin,
                ]),
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

            $codeUnique = new generateUniqueReference();
            // Préparer les données pour la notification
            $data = [
                'idProd' => $this->produit->id,
                'code_livr' => $codeUnique->generate(),
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
        if ($this->isProcessing) {
            return;
        }
        $this->isProcessing = true;

        try {
            $result = $this->appelOffreService->handleTakeaway([
                'notification' => $this->notification,
                'appeloffre' => $this->appeloffre,
                'produit' => $this->produit,
                'prixTotal' => $this->prixTotal,
                'prixFin' => $this->prixFin,
                'prixTrade' => $this->notification->data['prixTrade'],
            ]);

            session()->flash('success', $result['message']);
            $this->dispatch('refreshComponent');
        } catch (Exception $e) {
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
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

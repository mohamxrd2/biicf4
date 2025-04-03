<?php

namespace App\Livewire;

use App\Events\CountdownStarted;
use App\Events\NotificationSent;
use App\Jobs\ProcessCountdown;
use App\Models\AchatDirect;
use App\Models\AppelOffreGrouper;
use App\Models\Countdown;
use App\Models\Livraisons;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\livraisonAchatdirect;
use App\Notifications\RefusAchat;
use App\Services\RecuperationTimer;
use Carbon\Carbon;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB; // Pour utiliser les transactions
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;
use Intervention\Image\Facades\Image;


class Appeloffreterminergrouper extends Component
{
    use WithFileUploads;
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
    public $idProd2;

    //ciblage des livreur
    public $clientPays, $clientCommune, $clientContinent, $clientSous_Region, $clientDepartement,
        $AppelOffreGrouper, $prixFin, $produit, $Quantite;
    public $photoProd;
    public $textareaValue;
    public $groupages;
    public $timestamp;
    public $time;
    public $error;
    protected $recuperationTimer;
    public $countdownId;
    public $isRunning;
    public $timeRemaining;
    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }



    public function mount($id)
    {
        $this->timeServer();

        $this->notification = DatabaseNotification::findOrFail($id);
        $this->AppelOffreGrouper = AppelOffreGrouper::find($this->notification->data['id_appeloffre']);

        // Récupérer le produit/service correspondant
        $this->produit = ProduitService::where('reference', $this->AppelOffreGrouper->reference)
            ->where('user_id', Auth::id()) // Correction de la syntaxe Auth::id()
            ->first();

        // Vérifier si l'utilisateur a déjà soumis une quantité pour ce code unique
        $this->Quantite = userquantites::where('code_unique', $this->AppelOffreGrouper->codeunique)
            ->sum('quantite');

        // Charger les groupages
        $this->groupages = userquantites::where('code_unique', $this->AppelOffreGrouper->codeunique)
            ->orderBy('created_at', 'asc')
            ->get();

        $reduction = 0.1;
        $prixUnitaire = $this->notification->data['prixTrade'];
        $this->prixFin = $this->Quantite * $prixUnitaire * (1 - $reduction);

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
        $this->Idsender = $this->AppelOffreGrouper->user_id ?? null;

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
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
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
            $codeUnique = $this->AppelOffreGrouper->codeunique;
            if (!$codeUnique) {
                throw new Exception('Code unique non défini pour l\'Appel d\'Offre Groupé.');
            }

            $userQuantites = userquantites::where('code_unique', $codeUnique)->get();
            if ($userQuantites->isEmpty()) {
                throw new Exception('Code unique introuvable dans la table userquantites.');
            }

            // Téléchargez la photo
            $photoName = $this->handlePhotoUpload('photoProd');

            foreach ($userQuantites as $userQuantite) {
                $userId = $userQuantite->user_id;
                $quantite = $userQuantite->quantite;
                $localite = $userQuantite->localite;

                $userWallet = Wallet::where('user_id', $userId)->first();
                if (!$userWallet) {
                    Log::warning('Portefeuille introuvable', ['user_id' => $userId]);
                    continue;
                }

                $achatdirect = AchatDirect::create([

                    'data_finance' => json_encode([
                        'nameProd' => $this->produit->name,
                        'montantTotal' => $quantite * $this->notification->data['prixTrade'],
                        'prix' => $this->notification->data['prixTrade'],
                        'quantité' => $quantite,
                        'prix_negociation' => $this->notification->data['prixTrade'],
                        'prix_apres_comission' => $this->prixFin,
                        'localite' => $localite,
                        'date_tot' => $this->AppelOffreGrouper->dateTot,
                        'date_tard' => $this->AppelOffreGrouper->dateTard,
                    ]),
                    'type_achat' => 'appelOffreGrouper',
                    'userTrader' => Auth::id(),
                    'userSender' => $userId,
                    'photoProd' => $photoName,
                    'idProd' => $this->produit->id,
                    'code_unique' => $codeUnique,
                ]);

                $data = [
                    'idProd' => $this->produit->id,
                    'code_livr' => $this->generateUniqueReference(),
                    'textareaContent' => $validated['textareaValue'],
                    'photoProd' => $photoName,
                    'achat_id' => $achatdirect->id ?? null,
                    'title' => 'Négociations des livreurs',
                    'description' => 'Cliquez pour participer à la négociation',
                ];

                $this->startCountdown($data['code_livr'], $userId, $achatdirect->id);

                if ($this->livreursIds->isNotEmpty()) {
                    foreach ($this->livreursIds as $livreurId) {
                        $livreur = User::find($livreurId);
                        if ($livreur) {
                            Notification::send($livreur, new livraisonAchatdirect($data));
                            event(new NotificationSent($livreur));
                        }
                    }
                }
            }

            $this->notification->update(['reponse' => 'accepte']);
            DB::commit();

            $this->dispatch('formSubmitted', 'Commande acceptée avec succès. Notifications envoyées à tous les livreurs.');
            $this->modalOpen = false;
        } catch (Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Une erreur s\'est produite : ' . $e->getMessage());
            Log::error('Erreur lors du traitement de l\'achat', ['message' => $e->getMessage()]);
        }
    }
    private function calculateTimeValues($durationInMinutes)
    {
        $startTime = $this->timestamp;
        $endTime = $startTime->copy()->addMinutes($durationInMinutes);
        return [
            'start_time' => $startTime,
            'end_time' => $endTime,
            'time_remaining' => $durationInMinutes * 60,
        ];
    }

    public function startCountdown($code_unique, $userSender, $id)
    {
        try {
            // Calcul des valeurs de temps
            $timeValues = $this->calculateTimeValues(2); // Durée de 2 minutes

            $countdown = Countdown::firstOrCreate(
                ['code_unique' => $code_unique, 'is_active' => true],
                [
                    'user_id' => Auth::id(),
                    'userSender' => $userSender,
                    'start_time' => $timeValues['start_time'],
                    'difference' => 'ad',
                    'id_achat' => $id,
                    'time_remaining' => $timeValues['time_remaining'],
                    'end_time' => $timeValues['end_time'],
                ]
            );

            if ($countdown->wasRecentlyCreated) {
                dispatch(new ProcessCountdown($countdown->id, $code_unique))
                    ->onQueue('default')
                    ->afterCommit();

                event(new CountdownStarted(120, $code_unique));
            }
        } catch (Exception $e) {
            Log::error('Erreur lors du démarrage du compte à rebours', ['error' => $e->getMessage(), 'code_unique' => $code_unique]);
        }
    }


    public function refuser()
    {
        $userId = Auth::id(); // ID de l'utilisateur actuel
        $codeUnique = $this->notification->data['code_unique']; // Récupérer le code_unique depuis la notification

        DB::beginTransaction();

        try {
            // Récupérer toutes les entrées de userquantites associées au code_unique
            $userQuantites = UserQuantites::where('code_unique', $codeUnique)->get();

            if ($userQuantites->isEmpty()) {
                throw new Exception('Aucune entrée trouvée pour le code_unique : ' . $codeUnique);
            }

            // Parcourir chaque entrée et effectuer les opérations nécessaires
            foreach ($userQuantites as $userQuantite) {
                $clientId = $userQuantite->user_id; // Récupérer l'ID de l'utilisateur
                // Envoyer une notification au client
                $owner = User::findOrFail($clientId);
                Notification::send($owner, new RefusAchat($codeUnique));
                // Déclencher un événement pour signaler l'envoi de la notification
                event(new NotificationSent($owner));
            }

            // Mettre à jour la réponse de la notification principale
            $this->notification->update(['reponse' => 'refuser']);

            // Valider toutes les transactions
            DB::commit();

            session()->flash('success', 'Achat refusé avec succès pour tous les utilisateurs.');
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

    protected function generateIntegerReference(): int
    {
        // Récupère l'horodatage en millisecondes
        $timestamp = now()->getTimestamp() * 1000 + now()->micro;

        // Retourne l'horodatage comme entier
        return (int) $timestamp;
    }



    public function render()
    {
        return view('livewire.appeloffreterminergrouper');
    }
}

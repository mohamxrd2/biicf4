<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\Countdown;
use App\Models\offregroupe as ModelsAchatDirect;
use App\Models\OffreGroupe;
use App\Notifications\livraisonAppelOffregrouper;
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
use App\Models\userquantites;
use App\Notifications\RefusAchat;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use App\Notifications\livraisonAchatdirect;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;

class OffreNegosDone extends Component
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
    public $produit;
    public $offregroupe;
    public $offregroupeSom;
    public $offregroupef;
    public $prixFin;
    public $prixTotal;
    public $groupages;
    public $quantites;
    public $time;
    public $error;
    public $timestamp;

    protected $recuperationTimer;
    // Injection de la classe RecuperationTimer via le constructeur
    public function __construct()
    {
        $this->recuperationTimer = new RecuperationTimer();
    }

    public function mount($id)
    {
        $this->timeServer();


        // Récupération de la notification
        $this->notification = DatabaseNotification::findOrFail($id);

        // Récupération du produit lié
        $this->produit = ProduitService::findOrFail($this->notification->data['idProd']);

        // Récupération des offres groupées liées
        $this->offregroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->first();
        $this->quantites = userquantites::where('code_unique', $this->notification->data['code_unique'])
            ->sum('quantite');

        // Charger les groupages
        $this->groupages = userquantites::where('code_unique', $this->offregroupe->code_unique)
            ->orderBy('created_at', 'asc')
            ->get();
        // Calcul du prix total et du prix final
        $produitPrix = $this->offregroupe->produit->prix;
        $quantite = $this->quantites;

        $this->prixTotal = $produitPrix * $quantite;
        $this->prixFin = $this->prixTotal - ($this->prixTotal * 0.01); // Appliquer la réduction de 1%


        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
    }
    public function ciblageLivreurs()
    {
        // Vérification de l'existence de 'userSender' dans les données de la notification
        $this->Idsender = $this->offregroupe->user_id ?? null;

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
    public function accepter()
    {
        $this->timeServer();

        $validated = $this->validate([
            'photoProd' => 'required|image|max:1024', // Limite à 1 MB
            'textareaValue' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si le code_unique existe dans la table userquantites
            $codeUnique = $this->offregroupe->code_unique; // Assurez-vous que ce champ est correctement défini dans la requête
            $userQuantites = userquantites::where('code_unique', $codeUnique)->get();

            if ($userQuantites->isEmpty()) {
                session()->flash('error', 'Code unique introuvable dans la table userquantites.');
                return;
            }

            // Téléchargez la photo
            $photoName = $this->handlePhotoUpload('photoProd');

            // Parcourir chaque enregistrement dans userquantites et enregistrer l'achat pour chaque utilisateur
            foreach ($userQuantites as $userQuantite) {
                $userId = $userQuantite->user_id; // Récupérer l'ID utilisateur
                $quantite = $userQuantite->quantite; // Quantité saisie par l'utilisateur

                $userWallet = Wallet::where('user_id', $userId)->first();
                if (!$userWallet) {
                    Log::warning('Portefeuille introuvable pour l\'utilisateur', ['user_id' => $userId]);
                    continue; // Passez au suivant si le portefeuille est manquant
                }

                // Enregistrer l'achat dans la table AchatDirectModel
                $achatdirect = AchatDirect::create([
                    'photoProd' => $photoName,  // Quantité récupérée de userquantites
                    'prix' => $this->offregroupe->produit->prix,
                    'nameProd' => $this->produit->name,  // Quantité récupérée de userquantites
                    'quantité' => $quantite,  // Quantité récupérée de userquantites
                    'montantTotal' => $this->prixFin,
                    'localite' => 'cocody',
                    'date_tot' => now(),
                    'date_tard' => now(),
                    'userTrader' => Auth::id(),
                    'userSender' => $this->offregroupe->user_id,  // Utilisateur qui a saisi l'achat
                    'idProd' => $this->produit->id,
                    'code_unique' => $codeUnique,
                ]);

                // Préparer les données pour la notification
                $data = [
                    'idProd' => $this->produit->id,
                    'code_livr' => $this->notification->data['code_unique'],
                    'textareaContent' => $validated['textareaValue'],
                    'photoProd' => $photoName,
                    'achat_id' => $achatdirect->id ?? null,
                    'title' => 'Negociations des livreurs',
                    'description' => 'Cliquez pour particicper a la negociation',

                ];

                Countdown::create([
                    'user_id' => Auth::id(),
                    'userSender' => $userId,
                    'start_time' => $this->timestamp,
                    'difference' => 'ad',
                    'code_unique' => $data['code_livr'],
                    'id_achat' => $achatdirect->id,
                ]);

                if (!$data['idProd']) {
                    throw new Exception('Identifiant du produit introuvable.');
                }

                // Envoyer une notification aux livreurs pour la négociation
                if ($this->livreursIds->isNotEmpty()) {
                    foreach ($this->livreursIds as $livreurId) {
                        $livreur = User::find($livreurId);
                        if ($livreur) {
                            Notification::send($livreur, new livraisonAchatdirect($data));
                            event(new NotificationSent($livreur));
                            Log::info('Notification envoyée au livreur', ['livreur_id' => $livreur->id]);
                        }
                    }
                }
            }

            // Mettre à jour la notification après le traitement de tous les utilisateurs
            $this->notification->update(['reponse' => 'accepte']);

            DB::commit();

            // Retourner une confirmation
            $this->dispatch('formSubmitted', 'Commande acceptée avec succès. Notifications envoyées à tous les livreurs.');
            $this->modalOpen = false;
        } catch (Exception $e) {
            // Annuler la transaction et gérer l'erreur
            DB::rollBack();
            session()->flash('error', 'Une erreur s\'est produite : ' . $e->getMessage());
            Log::error('Erreur lors du traitement de l\'achat', ['message' => $e->getMessage()]);
        }
    }

    public function refuser()
    {
        // dd('');
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
                $FournisseurId = $userQuantite->user_id; // Récupérer l'ID de l'utilisateur

                $data = [
                    'id' => $this->offregroupe->id,
                    'idProd' => $this->notification->data['idProd'] ?? null,
                    'code_unique' => $this->notification->data['code_unique'],
                    'title' => 'Groupage Refusée',
                    'description' => 'Le fournisseur a annulé le groupage.',
                ];

                // Envoyer une notification au client
                $owner = User::findOrFail($FournisseurId);
                Notification::send($owner, new RefusAchat($data));
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
        return view('livewire.offre-negos-done');
    }
}

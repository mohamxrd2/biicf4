<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\offregroupe as ModelsAchatDirect;
use App\Models\OffreGroupe;
use App\Notifications\livraisonAppelOffregrouper;
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
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

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



    public function mount($id)
    {

        // Récupération de la notification
        $this->notification = DatabaseNotification::findOrFail($id);

        // Récupération du produit lié
        $this->produit = ProduitService::findOrFail($this->notification->data['idProd']);

        // Récupération des offres groupées liées
        $this->offregroupe = OffreGroupe::where('code_unique', $this->notification->data['code_unique'])->get();

        if ($this->offregroupe->isEmpty()) {
            throw new Exception('Aucune OffreGroupe trouvée pour le code unique : ' . $this->notification->data['code_unique']);
        }

        // Première OffreGroupe
        $this->offregroupef = $this->offregroupe->first();

        // Calcul de la somme des quantités
        $this->offregroupeSom = $this->offregroupe->sum('quantite');

        // Calcul du prix total et du prix final
        $produitPrix = $this->offregroupef->produit->prix;
        $quantite = $this->notification->data['quantite'];

        $this->prixTotal = $produitPrix * $quantite;
        $this->prixFin = $this->prixTotal - ($this->prixTotal * 0.01); // Appliquer la réduction de 1%


        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
    }

    public function ciblageLivreurs()
    {
        // Récupérer les informations du client
        $client = User::find($this->notification->data['id_sender']);

        if (!$client) {
            session()->flash('error', 'Client introuvable.');
            return;
        }

        // Normalisation des données du client pour comparaison
        $clientData = [
            'continent' => strtolower($client->continent),
            'sous_region' => strtolower($client->sous_region),
            'country' => strtolower($client->country),
            'departe' => strtolower($client->departe),
            'commune' => strtolower($client->commune),
        ];

        // Préparer les critères de filtrage pour les livreurs
        $query = Livraisons::query();

        $query->where(function ($q) use ($clientData) {
            $q->where(function ($subQuery) use ($clientData) {
                $subQuery->where('zone', 'proximite')
                    ->whereRaw('LOWER(continent) = ?', [$clientData['continent']])
                    ->whereRaw('LOWER(sous_region) = ?', [$clientData['sous_region']])
                    ->whereRaw('LOWER(pays) = ?', [$clientData['country']])
                    ->whereRaw('LOWER(departe) = ?', [$clientData['departe']])
                    ->whereRaw('LOWER(commune) = ?', [$clientData['commune']]);
            })
                ->orWhere(function ($subQuery) use ($clientData) {
                    $subQuery->where('zone', 'locale')
                        ->whereRaw('LOWER(continent) = ?', [$clientData['continent']])
                        ->whereRaw('LOWER(sous_region) = ?', [$clientData['sous_region']])
                        ->whereRaw('LOWER(pays) = ?', [$clientData['country']])
                        ->whereRaw('LOWER(departe) = ?', [$clientData['departe']]);
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

        // Filtrer les livreurs acceptés
        $this->livreurs = $query->where('etat', 'Accepté')->get();

        // Extraire les IDs et compter les livreurs
        $this->livreursIds = $this->livreurs->pluck('user_id');
        $this->livreursCount = $this->livreurs->count();

        // Vérification si aucun livreur n'est trouvé
        if ($this->livreursCount === 0) {
            session()->flash('info', 'Aucun livreur disponible correspondant aux critères.');
        }
    }


    public function accepter()
    {
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
                'reference' => $this->notification->data['code_unique'],
            ];

            if (!$data['idProd']) {
                throw new Exception('Identifiant du produit introuvable.');
            }

            // Envoyer les notifications aux livreurs
            if ($this->livreursIds->isNotEmpty()) {
                foreach ($this->livreursIds as $livreurId) {
                    $livreur = User::find($livreurId);
                    if ($livreur) {
                        Notification::send($livreur, new livraisonAppelOffregrouper($data));
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

    public function refuser()
    {
        $userId = Auth::id();
        $clientId = $this->offregroupe->userSender;
        $montantTotal = $this->offregroupe->montantTotal;

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
                'Achat refusé',
                'effectué',
                'COC'
            );

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
            // Vérifiez que notification et offregroupe sont définis
            if (!$this->notification || !$this->offregroupe) {
                Log::error('Notification ou offregroupe non défini.', [
                    'notification' => $this->notification,
                    'offregroupe' => $this->offregroupe,
                ]);
                session()->flash('error', 'Données manquantes pour traiter la demande.');
                return;
            }

            // Préparer les détails pour la notification
            $details = [
                'prixFin' =>  $this->prixFin ?? null,
                'code_unique' => $this->offregroupe->code_unique ?? null,
                'achat_id' => $this->offregroupe->id ?? null,
            ];

            // Trouvez l'utilisateur expéditeur
            $userSender = User::find($this->offregroupe->userSender);


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
        return view('livewire.offre-negos-done');
    }
}

<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\AppelOffreGrouper;
use App\Models\Livraisons;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\AllerChercher;
use App\Notifications\CountdownNotification;
use App\Notifications\livraisonAchatdirect;
use App\Notifications\livraisonAppelOffregrouper;
use App\Notifications\livraisonVerif;
use App\Notifications\RefusAchat;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB; // Pour utiliser les transactions
use Illuminate\Support\Str;
use Livewire\Component;

class Appeloffreterminergrouper extends Component
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
    public $idProd2;

    //ciblage des livreur
    public $clientPays;
    public $clientCommune;
    public $clientContinent;
    public $clientSous_Region;
    public $clientDepartement;
    public $AppelOffreGrouper;
    public $prixFin;
    public $produit;



    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->AppelOffreGrouper = AppelOffreGrouper::find($this->notification->data['AppelOffreGrouper_id']);
        $this->produit = ProduitService::find($this->AppelOffreGrouper->id_prod);
        $this->prixFin = $this->notification->data['quantiteTotal'] - $this->notification->data['quantiteTotal']  * 0.01;

        $this->ciblageLivreurs();

        //ciblage de livreur
        $this->nombreLivr = User::where('actor_type', 'livreur')->count();
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
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function accepter()
    {
        $validated = $this->validate([
            'photoProd' => 'required|image|max:1024', // Limite à 1 MB
            'textareaValue' => 'required',
        ]);

        DB::beginTransaction();
        try {
            // Vérifier si le code_unique existe dans la table userquantites
            $codeUnique = $validated['codeUnique']; // Assurez-vous que ce champ est correctement défini dans la requête
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
                    'quantité' => $quantite,  // Quantité récupérée de userquantites
                    'montantTotal' => $quantite * $this->notification->data['prixTrade'],
                    'localite' => $this->AppelOffreGrouper->localite,
                    'date_tot' => $this->AppelOffreGrouper->dateTot,
                    'date_tard' => $this->AppelOffreGrouper->dateTard,
                    'userTrader' => Auth::id(),
                    'userSender' => $userId,  // Utilisateur qui a saisi l'achat
                    'idProd' => $this->produit->id,
                    'code_unique' => $codeUnique,
                ]);
                // Préparer les données pour la notification
                $data = [
                    'idProd' => $this->notification->data['idProd'] ?? null,
                    'code_livr' => $this->notification->data['code_unique'],
                    'textareaContent' => $validated['textareaValue'],
                    'photoProd' => $photoName,
                    'achat_id' => $achatdirect->id ?? null,
                ];

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
                $montantTotal = $userQuantite->quantite * $this->achatdirect->prix_unitaire; // Calculer le montant total basé sur la quantité et le prix unitaire

                // Vérifier l'existence du portefeuille de l'utilisateur
                $userWallet = Wallet::where('user_id', $clientId)->firstOrFail();

                // Ajouter le montant au portefeuille de l'utilisateur
                $userWallet->increment('balance', $montantTotal);

                // Générer une référence unique
                $reference_id = $this->generateIntegerReference();

                // Créer une transaction pour ce client
                $this->createTransaction(
                    $userId, // ID de l'utilisateur qui a refusé
                    $clientId, // ID du client
                    'Réception', // Type de transaction
                    $montantTotal, // Montant total
                    $reference_id, // Référence unique
                    'Achat refusé pour le code : ' . $codeUnique, // Description
                    'effectué', // Statut
                    'COC' // Code d'opération
                );

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

            // Récupérer le code_unique depuis l'achat direct
            $codeUnique = $this->achatdirect->code_unique ?? null;

            if (!$codeUnique) {
                throw new Exception('Code unique introuvable.');
            }

            // Récupérer toutes les entrées de userquantites liées au code_unique
            $userQuantites = UserQuantites::where('code_unique', $codeUnique)->get();

            if ($userQuantites->isEmpty()) {
                throw new Exception('Aucune donnée trouvée dans userquantites pour ce code unique : ' . $codeUnique);
            }

            // Parcourir les entrées userquantites et traiter chaque utilisateur
            foreach ($userQuantites as $userQuantite) {
                $userSenderId = $userQuantite->user_id; // ID de l'utilisateur
                $quantite = $userQuantite->quantite; // Quantité saisie par l'utilisateur
                $montantTotal = $quantite * $this->notification->data['prixTrade']; // Calculer le montant total

                // Créer une entrée AchatDirect pour cet utilisateur
                $achatDirect = AchatDirect::create([
                    'quantité' => $quantite,
                    'montantTotal' => $montantTotal,
                    'localite' => $this->AppelOffreGrouper->localite ?? null,
                    'date_tot' => $this->AppelOffreGrouper->dateTot ?? null,
                    'date_tard' => $this->AppelOffreGrouper->dateTard ?? null,
                    'userTrader' => Auth::id(),
                    'userSender' => $userSenderId,
                    'idProd' => $this->produit->id,
                    'code_unique' => $codeUnique,
                ]);

                // Préparer les détails pour la notification
                $details = [
                    'prixFin' => $this->prixFin ?? null,
                    'code_unique' => $codeUnique,
                    'achat_id' => $achatDirect->id,
                ];

                // Récupérer l'utilisateur expéditeur
                $userSender = User::findOrFail($userSenderId);

                // Envoyer une notification au client
                Notification::send($userSender, new CountdownNotificationAd($details));
                event(new NotificationSent($userSender));
            }

            // Mettre à jour la notification originale
            $this->notification->update(['reponse' => 'accepte', 'type_achat' => 'Take Away']);
            Log::info('Notification originale mise à jour avec succès.', [
                'notificationId' => $this->notification->id,
            ]);

            // Valider la transaction
            DB::commit();

            session()->flash('success', 'Take Away traité avec succès pour tous les utilisateurs.');
        } catch (Exception $e) {
            // Annuler la transaction si un élément est introuvable
            DB::rollBack();
            session()->flash('error', 'Une erreur s\'est produite : ' . $e->getMessage());
            Log::error('Erreur dans la méthode takeaway', ['message' => $e->getMessage()]);
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



    public function render()
    {
        return view('livewire.appeloffreterminergrouper');
    }
}

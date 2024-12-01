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

    public function accepter()
    {
        $validated = $this->validate([
            'photoProd' => 'required|image|max:1024', // Limite à 1 MB
            'textareaValue' => 'required',
        ]);

        DB::beginTransaction();
        try {

           

            // Téléchargez la photo
            $photoName = $this->handlePhotoUpload('photoProd');

            // Parcourir chaque enregistrement dans userquantites et enregistrer l'achat pour chaque utilisateur
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



            // Parcourir les entrées userquantites et traiter chaque utilisateur

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

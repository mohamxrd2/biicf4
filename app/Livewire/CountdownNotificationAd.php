<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\ComissionAdmin;
use App\Models\Gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\Confirmation;
use App\Notifications\mainleve;
use App\Notifications\mainleveAd;
use App\Notifications\RefusAchat;
use App\Notifications\VerifUser;
use App\Services\AchatDirectService;
use App\Services\CommissionService;
use App\Services\RetraitMagasinService;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class CountdownNotificationAd extends Component
{

    public $notification, $id, $produit, $userFour, $totalPrice, $user, $achatdirect, $livreur, $codeVerification,
        $fournisseur, $userWallet, $quantite,
        $qualite, $diversite, $userId, $userWalletFournisseur, $requiredAmount, $statusText, $statusClass, $prix_negociation;

    public $showMainlever = false;
    public $isLoading = false;

    protected $listeners = ['refreshComponent' => '$refresh'];
    protected $achatDirectService;

    public function boot(AchatDirectService $achatDirectService)
    {
        $this->achatDirectService = $achatDirectService;
    }

    public function mount($id)
    {
        try {
            $this->notification = DatabaseNotification::findOrFail($id);
            $this->userFour = User::find($this->notification->data['fournisseur'] ?? null);
            $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié
            $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
            $this->codeVerification = $this->achatdirect->code_verification;

            if (!$this->achatdirect) {
                throw new Exception("AchatDirect introuvable avec l'ID fourni.");
            }

            // Décoder le JSON stocké dans data_finance
            $dataFinance = json_decode($this->achatdirect->data_finance, true);

            // Accéder à la valeur de prix_final
            $this->prix_negociation = $dataFinance['prix_negociation'] ?? 0;

            $this->fournisseur = User::find($this->achatdirect->userTrader);
            $this->livreur = User::find($this->notification->data['livreur']);
            $this->userId = User::find($this->achatdirect->userSender);
            $this->produit = ProduitService::find($this->achatdirect->idProd);

            if (!$this->produit) {
                throw new Exception("ProduitService introuvable avec l'ID fourni.");
            }

            $this->userWallet = Wallet::where('user_id', $this->user)->first();
            $this->userWalletFournisseur = Wallet::where('user_id', $this->fournisseur->id)->first();

            if (!$this->userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $this->user]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            // Récupération du statut de la notification
            $status = $this->getNotificationStatus($this->notification->reponse);
            $this->statusText = $status['text'];
            $this->statusClass = "{$status['bg']} {$status['textColor']}";
            Log::info('Portefeuille trouvé', ['userWallet' => $this->userWallet]);
        } catch (Exception $e) {
            Log::error("Erreur dans mount : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de la récupération des données.');
        }
    }

    public function toggleComponent()
    {
        // Simulation d'un délai (à supprimer dans un environnement de production)
        sleep(1); // Simuler un délai de 1 seconde (PHP)
        $this->showMainlever = true;
    }
    public function getNotificationStatus($response)
    {
        return match ($response) {
            'accepter' => ['text' => 'effectué', 'bg' => 'bg-green-100', 'textColor' => 'text-green-700'],
            'refuser' => ['text' => 'refusé', 'bg' => 'bg-red-100', 'textColor' => 'text-red-700'],
            default => ['text' => 'en attente', 'bg' => 'bg-yellow-100', 'textColor' => 'text-yellow-700'],
        };
    }
    public function FactureRefuser()
    {
        $this->isLoading = true;
        try {
            // Vérification des données récupérées
            if (!$this->achatdirect) {
                throw new Exception("Données introuvables pour le livreur ou l'achat.");
            }

            $livreurdetails = [
                'id' => $this->achatdirect->id_achat,
                'idProd' => $this->produit->id,
                'code_unique' => $this->notification->data['code_unique'],
                'title' => 'Facture Refusée',
                'description' => 'Le prix de la facture ne convient pas au client',
            ];

            // Envoi de la notification au fournisseur
            if ($this->fournisseur) {
                Notification::send($this->fournisseur, new RefusAchat($livreurdetails));
                event(new NotificationSent($this->fournisseur));
            } else {
                Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->achatdirect->id_achat);
            }

            // Envoi de la notification au livreur
            if ($this->livreur) {
                Notification::send($this->livreur, new Confirmation($livreurdetails));
                event(new NotificationSent($this->livreur));
            } else {
                Log::warning("Livreur introuvable pour la notification ID : " . $this->notification->id);
            }

            // Mise à jour de l'état de la notification
            $this->notification->update(['reponse' => 'refuser']);
            $this->reset('showMainlever');

            session()->flash('success', 'La facture a été refusée et les notifications ont été envoyées.');
            $this->isLoading = false;
        } catch (Exception $e) {
            $this->isLoading = false;
            Log::error("Erreur dans FactureRefuser : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de l\'envoi des notifications.');
        }
    }

    public function valider()
    {
        $this->isLoading = true;
        $this->requiredAmount = floatval($this->notification->data['prixTrade']);

        DB::beginTransaction();
        try {
            switch ($this->achatdirect->type_achat) {
                case 'appelOffreGrouper':
                case 'appelOffre':
                case 'achatDirect':
                case 'OffreGrouper':

                    $result = $this->achatDirectService->handlePourLivraison([
                        'achatdirect' => $this->achatdirect,
                        'userWallet' => $this->userWallet,
                        'notification' => $this->notification,
                        'fournisseur' => $this->fournisseur,
                        'userId' => $this->user,
                        'requiredAmount' => $this->requiredAmount,
                    ]);
                    break;
            }


            // Décoder l'ancien JSON en tableau associatif (éviter l'écrasement)
            $dataFinance = json_decode($this->achatdirect->data_finance, true) ?? [];

            // Ajouter la nouvelle valeur sans supprimer les anciennes
            $dataFinance['prix_livraison'] = $this->notification->data['prixTrade'];

            // Mettre à jour la colonne `data_finance`
            $this->achatdirect->update([
                'data_finance' => json_encode($dataFinance),
            ]);




            if (isset($result['success'])) {
                session()->flash('success', $result['message']);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la validation', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', $e->getMessage() ?: 'Une erreur est survenue lors du processus de validation. Veuillez réessayer.');
        } finally {
            $this->isLoading = false;
        }
    }

    public function mainleve()
    {
        DB::beginTransaction();
        try {
            // Rassemblez les réponses dans un tableau
            $responses = [
                'Quantité' => $this->quantite,
                'Qualité' => $this->qualite,
                'Diversité' => $this->diversite,
            ];

            // Comptez le nombre de "oui"
            $countYes = count(array_filter([$this->quantite, $this->qualite, $this->diversite], fn($value) => $value === 'oui'));

            // Vérifiez la condition
            if ($countYes < 2) {
                session()->flash('error', 'Vous devez sélectionner au moins deux réponses "OUI" pour continuer.');
                return;
            }
            // Générer deux codes distincts
            $fournisseurCode = random_int(1000, 9999);
            $livreurCode = random_int(1000, 9999);

            if (!$this->livreur && $this->notification->data['type_achat'] == 'Take Away') {
                $data = [
                    'notification' => $this->notification,
                    'user' => $this->user,
                    'fournisseur' => $this->fournisseur,
                    'userWalletFournisseur' => $this->userWalletFournisseur,
                    'achatdirect' => $this->achatdirect,
                    'produit' => $this->produit,
                    'userId' => $this->userId,
                    'codeVerification' => $this->codeVerification
                ];

                $service = new RetraitMagasinService($data);
                $service->retraitMagasin();
            } else {

                // Préparer les données pour le livreur
                $dataLivreur = [
                    'code_unique' => $this->achatdirect->code_unique,
                    'livreurCode' => $livreurCode,
                    'fournisseurCode' => $fournisseurCode,
                    'fournisseur' => $this->fournisseur->id ?? null,
                    'livreur' => $this->notification->data['livreur'],
                    'client' => $this->achatdirect->userSender ?? null,
                    'achat_id' => $this->achatdirect->id ?? null,
                    'prixTrade' => $this->notification->data['prixTrade'] ?? null,
                    'title' => 'Livraison a effectuer',
                    'description' => 'Deplacez vous pour aller chercher le colis->',
                ];

                if ($this->livreur) {
                    Notification::send($this->livreur, new mainleveAd($dataLivreur));
                    event(new NotificationSent($this->livreur));
                }
            }
            $this->notification->update(['reponse' => 'mainleveclient']);
            // Réinitialisation et fermeture du modal
            $this->reset('showMainlever');
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            // Gérer les exceptions générales
            Log::error('Erreur lors de la validation', [
                'message' => $e->getMessage(),

            ]);
            session()->flash('error', 'Une erreur est survenue lors du processus de validation. Veuillez réessayer.');
        }
    }


    public function render()
    {
        return view('livewire.countdown-notification-ad');
    }
}

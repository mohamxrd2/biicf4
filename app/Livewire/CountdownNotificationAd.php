<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\ComissionAdmin;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\commandVerif;
use App\Notifications\commandVerifAd;
use App\Notifications\Confirmation;
use App\Notifications\mainleve;
use App\Notifications\mainleveAd;
use App\Notifications\RefusAchat;
use App\Notifications\VerifUser;
use App\Services\AchatDirectService;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class CountdownNotificationAd extends Component
{

    public $notification;
    public $id;
    public $produit;
    public $userFour;
    public $totalPrice;
    public $user;
    public $achatdirect;
    public $livreur;
    public $codeVerification;
    public $fournisseur;
    public $userWallet;
    public $quantite;
    public $qualite;
    public $diversite;
    public $userId;
    public $userWalletFournisseur;
    public $requiredAmount;
    public $statusText;
    public $statusClass;
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
                    $result = $this->achatDirectService->handlePourLivraison([
                        'achatdirect' => $this->achatdirect,
                        'userWallet' => $this->userWallet,
                        'notification' => $this->notification,
                        'fournisseur' => $this->fournisseur,
                        'userId' => $this->user,
                        'requiredAmount' => $this->requiredAmount
                    ]);
                    break;

                case 'OffreGrouper':
                    $result = $this->achatDirectService->handleAchatDirectPoffreGroupe([
                        'achatdirect' => $this->achatdirect,
                        'userWallet' => $this->userWallet,
                        'notification' => $this->notification,
                        'fournisseur' => $this->fournisseur,
                        'userId' => $this->user,
                        'requiredAmount' => $this->requiredAmount
                    ]);
                    break;
            }

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


    private function retait_magasin()
    {

        // Calcul du montant requis avec une réduction de 1% cest pour le retrait en magasin
        $requiredAmount = floatval($this->notification->data['prixFin']);

        // Vérification de l'existence de l'achat dans les transactions gelées
        $existingGelement = gelement::where('reference_id', $this->notification->data['code_unique'])
            ->first();

        if (!$existingGelement || $existingGelement->amount < $requiredAmount) {
            Log::warning('Montant insuffisant ou aucune transaction gelée trouvée.', [
                'reference_id' => $this->notification->data['code_unique'],
                'required_amount' => $requiredAmount,
                'available_amount' => $existingGelement->amount ?? 0,
            ]);
            session()->flash('error', 'Le montant requis est insuffisant dans les transactions gelées.');
            return;
        }

        DB::beginTransaction(); // Démarre une transaction pour garantir la cohérence
        try {

            // Retirer le montant du gel
            $existingGelement->amount -= $requiredAmount;
            $existingGelement->save();

            // met a jour le portefeuille de l'Fournisseur
            $this->userWalletFournisseur->balance += $requiredAmount;
            $this->userWalletFournisseur->save();

            $this->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Envoie',
                $this->achatdirect->montantTotal,
                $this->generateIntegerReference(),
                'Debité pour achat',
                'effectué',
                'COC'
            );
            $this->createTransaction(
                $this->user,
                $this->fournisseur->id ?? null,
                'Réception',
                $requiredAmount,
                $this->generateIntegerReference(),
                'Réception pour achat',
                'effectué',
                'COC'
            );

            // Calcul des commissions
            // $roi = $this->achatdirect->montantTotal * 0.01 / 100;
            $commissions = $this->achatdirect->montantTotal - $requiredAmount;

            // Paiement des commissions aux parrains
            $this->distributeCommissions($commissions);

            // Préparer les données pour le fournisseur
            $dataFournisseur = [
                'code_unique' => $this->achatdirect->code_unique,
                'id' => $this->achatdirect->id ?? null,
                'idProd' => $this->produit->id ?? null,
                'title' => 'Commande récupérée avec succès',
                'description' => 'Votre commande a été récupérée avec succès. Merci de votre confiance !',
            ];

            if ($this->fournisseur) {
                Notification::send($this->fournisseur, new Confirmation($dataFournisseur));
                event(new NotificationSent($this->fournisseur));

                Log::info('Notification envoyée au fournisseur', ['fournisseurId' => $this->fournisseur->id, 'code' => $this->codeVerification]);
            }

            if ($this->userId) {
                Notification::send($this->userId, new Confirmation($dataFournisseur));
            } else {
                Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->achatdirect->id);
            }
            DB::commit(); // Valide la transaction
        } catch (Exception $e) {
            DB::rollBack(); // Annule les modifications en cas d'erreur
            Log::error('Erreur lors du traitement de la livraison.', [
                'message' => $e->getMessage(),
                'user_id' => $this->userWallet->user_id,
                'required_amount' => $requiredAmount
            ]);
            session()->flash('error', 'Une erreur s\'est produite lors du traitement de la livraison.');
        }
    }
    private function distributeCommissions($commissions)
    {
        if ($this->fournisseur->parrain) {
            $currentParrain = $this->fournisseur;

            for ($level = 1; $level <= 2; $level++) {
                if (!$currentParrain->parrain) break;

                $nextParrain = User::find($currentParrain->parrain);
                $wallet = Wallet::where('user_id', $nextParrain->id)->first();

                if ($wallet) {
                    $commissionAmount = $commissions * 0.01;
                    $wallet->balance += $commissionAmount;
                    $wallet->save();

                    Log::info("Commission envoyée au parrain niveau $level", [
                        'parrain_id' => $nextParrain->id,
                        'commissions' => $commissionAmount,
                    ]);

                    $this->createTransaction(
                        $this->user,
                        $nextParrain->id,
                        'Commission',
                        $commissionAmount,
                        $this->generateIntegerReference(),
                        'Commission de BICF',
                        'effectué',
                        'COC'
                    );

                    $commissions -= $commissionAmount;
                }

                $currentParrain = $nextParrain;
            }
        }
        if ($this->userId->parrain) {
            $currentParrain = $this->userId;

            for ($level = 1; $level <= 2; $level++) {
                if (!$currentParrain->parrain) break;

                $nextParrain = User::find($currentParrain->parrain);
                $wallet = Wallet::where('user_id', $nextParrain->id)->first();

                if ($wallet) {
                    $commissionAmount = $commissions * 0.01;
                    $wallet->balance += $commissionAmount;
                    $wallet->save();

                    Log::info("Commission envoyée au parrain niveau $level", [
                        'parrain_id' => $nextParrain->id,
                        'commissions' => $commissionAmount,
                    ]);

                    $this->createTransaction(
                        $this->user,
                        $nextParrain->id,
                        'Commission',
                        $commissionAmount,
                        $this->generateIntegerReference(),
                        'Commission de BICF',
                        'effectué',
                        'COC'
                    );

                    $commissions -= $commissionAmount;
                }

                $currentParrain = $nextParrain;
            }
        }

        // Commission pour l'admin
        $adminWallet = ComissionAdmin::where('admin_id', 1)->first();
        if ($adminWallet) {
            $adminWallet->balance += $commissions;
            $adminWallet->save();

            Log::info('Commission envoyée à l\'admin', [
                'admin_id' => 1,
                'commissions' => $commissions,
            ]);

            $this->createTransactionAdmin(
                $this->user,
                1,
                'Commission',
                $commissions,
                $this->generateIntegerReference(),
                'Commission de BICF',
                'effectué',
                'commission'
            );
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

            if (!$this->livreur && $this->notification->type_achat == 'Take Away') {
                $this->retait_magasin();
            } else {
                // Préparer les données pour le fournisseur

                $dataFournisseur = [
                    'code_unique' => $this->achatdirect->code_unique,
                    'fournisseurCode' => $fournisseurCode,
                    'livreurCode' => $livreurCode,
                    'livreur' => $this->notification->data['livreur'],
                    'fournisseur' => $this->fournisseur->id ?? null,
                    'client' => $this->achatdirect->userSender ?? null,
                    'achat_id' => $this->achatdirect->id ?? null,
                    'title' => 'Recuperation de la commande',
                    'description' => 'Remettez le colis au livreur.',
                ];

                if ($this->fournisseur) {
                    Notification::send($this->fournisseur, new mainleveAd($dataFournisseur));
                    event(new NotificationSent($this->fournisseur));

                    Log::info('Notification envoyée au fournisseur', ['fournisseurId' => $this->fournisseur->id, 'code' => $fournisseurCode]);
                }

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

                    Log::info('Notification envoyée au livreur', ['livreurId' => $this->livreur->id, 'code' => $livreurCode]);
                }
            }
            $this->notification->update(['reponse' => 'mainleveclient']);
            // Réinitialisation et fermeture du modal
            $this->reset('showMainlever');
            session()->flash('message', 'Livraison marquée comme livrée.');
            DB::commit();
        } catch (Exception $e) {
            // Gérer les exceptions générales
            Log::error('Erreur lors de la validation', [
                'message' => $e->getMessage(),

            ]);
            session()->flash('error', 'Une erreur est survenue lors du processus de validation. Veuillez réessayer.');
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
    protected function createTransactionAdmin(int $senderId, int $receiverId, string $type, float $amount, int $reference_id, string $description, string $status, string $type_compte): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_admin_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = $status;
        $transaction->type_compte = $type_compte;

        $transaction->save();
    }

    public function render()
    {
        return view('livewire.countdown-notification-ad');
    }
}

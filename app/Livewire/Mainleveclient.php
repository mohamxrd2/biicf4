<?php

namespace App\Livewire;

use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use App\Models\ComissionAdmin;
use App\Models\Gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\Confirmation;
use App\Notifications\RefusAchat;
use App\Notifications\RefusVerif;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use App\Services\TransactionService;
use App\Services\NotificationService;
use App\Services\WalletService;
use App\Services\CommissionService;

class Mainleveclient extends Component
{

    public $code_verif, $totalPrice, $notification, $id, $achatdirect, $appeloffre, $livreur,
        $fournisseurWallet, $quantite, $qualite, $user, $userWallet, $diversite, $montantTotal,
        $gelement, $showMainlever = false;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        $this->livreur = User::find($this->notification->data['livreur']);

        $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié
        $this->userWallet = Wallet::where('user_id', $this->user)->first();

        // Décoder le JSON stocké dans data_finance
        $dataFinance = json_decode($this->achatdirect->data_finance, true);

        // Accéder à la valeur de prix_final
        $this->montantTotal = $dataFinance['montantTotal'] + $dataFinance['prix_livraison'];
        $this->gelement = Gelement::where('reference_id', $this->notification->data['code_unique'])
            ->where('id_wallet', $this->userWallet->id)
            ->first();
    }

    public function toggleComponent()
    {
        $this->showMainlever = true;
    }
    public function getCodeVerifProperty()
    {
        // Nettoie le code en enlevant les espaces blancs
        return trim($this->code_verif);
    }

    public function verifyCode()
    {
        // Validation du code de vérification
        $this->validate([
            'code_verif' => 'required|string|size:4', // Taille de 4 caractères
        ], [
            'code_verif.required' => 'Le code de vérification est requis.',
            'code_verif.string' => 'Le code de vérification doit être une chaîne.',
            'code_verif.size' => 'Le code de vérification doit être exactement de 4 caractères.',
        ]);

        if (trim($this->code_verif) === trim($this->notification->data['CodeVerification'])) {
            session()->flash('succes', 'Code valide. Procédez a la main lévee');
        } else {
            session()->flash('error', 'Code invalide.');
        }
    }
    public function acceptColis()
    {
        $transactionService = new TransactionService();
        $notificationService = new NotificationService();
        $walletService = new WalletService();
        $commissionService = new CommissionService();

        DB::beginTransaction();
        try {
            // Validation des réponses
            $responses = [
                'Quantité' => $this->quantite,
                'Qualité' => $this->qualite,
                'Diversité' => $this->diversite,
            ];
            $countYes = count(array_filter($responses, fn($value) => strtolower($value) === 'oui'));

            if ($countYes < 2) {
                session()->flash('error', 'Vous devez sélectionner au moins deux réponses "OUI" pour continuer.');
                return;
            }

            // Traitement selon le type d'achat
            switch ($this->achatdirect->type_achat) {
                case 'OffreGrouper':
                    $this->processGroupedPayments($transactionService, $walletService, $commissionService, $notificationService);

                    break;
                case 'achatDirect':
                case 'appelOffreGrouper':
                case 'appelOffre':
                    $this->processAchatdirectPayments($transactionService, $walletService, $commissionService, $notificationService);
                    break;
                default:
                    throw new Exception('Type d\'achat inconnu.');
            }

            // Mise à jour de la notification
            $this->notification->update(['reponse' => 'Confirmation']);
            session()->flash('succes', 'Le paiement a été effectué avec succès.');
            $this->reset('showMainlever');

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Portefeuille introuvable.', ['message' => $e->getMessage()]);
            session()->flash('error', 'Un portefeuille requis est introuvable.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'acceptation du colis.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }

    /**
     * Traitement des paiements pour achat direct
     */
    private function processAchatdirectPayments($transactionService, $walletService, $commissionService, $notificationService)
    {
        if (!$this->gelement) {
            throw new Exception('Référence introuvable dans la table gelement.');
        }

        $fournisseurId = $this->notification->data['fournisseur'];
        $livreurId = $this->notification->data['livreur'];
        $prixTrade = $this->notification->data['prixTrade'];
        $valeurGelement = $this->gelement->amount;

        // Décoder `data_finance` pour éviter l'erreur
        $dataFinance = json_decode($this->achatdirect->data_finance, true) ?? [];

        // Vérifier si les clés existent avant d'y accéder
        $interetFournisseur = ($dataFinance['montantTotal'] ?? 0) * 0.1;
        $montantPourFournisseur = $dataFinance['prix_apres_comission'] ?? 0;

        $interetLivreur = ($dataFinance['prix_livraison'] ?? 0) * 0.1;
        $montantPourLivreur = ($dataFinance['prix_livraison'] ?? 0) - $interetLivreur;

        $totalInterets = $interetFournisseur + $interetLivreur;

        $this->gelement->amount -=  $montantPourFournisseur + $montantPourLivreur;


        $walletService->updateBalance($fournisseurId, $montantPourFournisseur);
        $walletService->updateBalance($livreurId, $montantPourLivreur);
        $referenceId = $this->generateIntegerReference();

        $transactionService->createTransaction(
            Auth::id(),
            $fournisseurId,
            'Réception',
            $montantPourFournisseur,
            $referenceId,
            'Paiement pour achat.',
            'COC'
        );

        $transactionService->createTransaction(
            Auth::id(),
            $livreurId,
            'Réception',
            $montantPourLivreur,
            $referenceId,
            'Paiement pour livraison.',
            'COC'
        );
        $transactionService->createTransaction(
            Auth::id(),
            Auth::id(),
            'Envoie',
            $valeurGelement,
            $referenceId,
            'Paiement de l\'achat',
            'COC'
        );

        $fournisseur = User::findOrFail($fournisseurId);
        $commissionService->handleCommissions($totalInterets, $fournisseur->parrain);

        // Notification des utilisateurs
        $notificationService->notifyUsers([
            'code_unique' => $this->notification->data['code_unique'],
            'achat_id' => $this->notification->data['achat_id'],
            'title' => 'Transaction réussie',
            'description' => 'Votre paiement a été traité avec succès. Merci pour votre confiance !',
        ], [$fournisseurId, $livreurId]);

        $this->gelement->amount -= $totalInterets + $montantPourFournisseur + $montantPourLivreur;
        $this->gelement->status = 'OK';
        $this->gelement->save();

        return true;
    }

    /**
     * Traitement des paiements pour offre groupée
     */
    private function processGroupedPayments($transactionService, $walletService, $commissionService, $notificationService)
    {
        // Décoder `data_finance` pour éviter l'erreur
        $dataFinance = json_decode($this->achatdirect->data_finance, true) ?? [];

        $prixUnitaire = $this->achatdirect->prix;
        $codeUnique = $this->achatdirect->code_unique;
        $livreurId = $this->notification->data['livreur'] ?? null;
        $valeurGelement = $this->gelement->amount ?? 0;

        if (!$livreurId || !$valeurGelement) {
            throw new Exception('Données de paiement invalides pour l\'offre groupée.');
        }

        // Calcul de l'intérêt pour le livreur
        $interetLivreur = $dataFinance['prix_livraison'] * 0.1;
        $montantPourLivreur = $dataFinance['prix_livraison'] - $interetLivreur;
        $walletService->updateBalance($livreurId, $montantPourLivreur);

        $userQuantites = userquantites::where('code_unique', $codeUnique)->get();
        if ($userQuantites->isEmpty()) {
            throw new Exception('Aucune quantité utilisateur trouvée pour l\'offre groupée.');
        }

        // Initialiser le total des intérêts avec l'intérêt du livreur
        $totalInterets = $interetLivreur;



        foreach ($userQuantites as $userQuantite) {
            $userId = $userQuantite->user_id;
            $quantite = $userQuantite->quantite;

            $prixTotal = $prixUnitaire * $quantite;
            $interetFournisseur = ($valeurGelement - $prixTotal) * 0.1;
            $montantPourFournisseur = $prixTotal - $interetFournisseur;

            $walletService->updateBalance($userId, $montantPourFournisseur);
            $totalInterets += $interetFournisseur;
            $referenceId = $this->generateIntegerReference();

            $transactionService->createTransaction(
                Auth::id(),
                $userId,
                'Réception',
                $montantPourFournisseur,
                $referenceId,
                'Réception pour achat groupé.',
                'COC'
            );

            // Notification des utilisateurs
            $notificationService->notifyUsers([
                'code_unique' => $this->notification->data['code_unique'],
                'achat_id' => $this->notification->data['achat_id'],
                'title' => 'Transaction réussie',
                'description' => 'Votre paiement a été traité avec succès. Merci pour votre confiance !',
            ], [$userId]);
        }

        $transactionService->createTransaction(
            Auth::id(),
            $livreurId,
            'Réception',
            $montantPourLivreur,
            $referenceId,
            'Paiement pour livraison.',
            'COC'
        );

        $transactionService->createTransaction(
            Auth::id(),
            Auth::id(),
            'Envoie',
            $valeurGelement,
            $referenceId,
            'Paiement de l\'achat',
            'COC'
        );

        $fournisseur = User::findOrFail(Auth::id());
        $commissionService->handleCommissions($totalInterets, $fournisseur->parrain);

        // Notification des utilisateurs
        $notificationService->notifyUsers([
            'code_unique' => $this->notification->data['code_unique'],
            'achat_id' => $this->notification->data['achat_id'],
            'title' => 'Transaction réussie',
            'description' => 'Votre paiement a été traité avec succès. Merci pour votre confiance !',
        ], [$livreurId]);

        $this->gelement->amount -= $dataFinance['montantTotal'];
        $this->gelement->status = 'OK';
        $this->gelement->save();
    }

    public function refuseColis()
    {

        DB::beginTransaction();
        try {
            $dataType = $this->achatdirect ? 'achatdirect' : ($this->appeloffre ? 'appeloffre' : null);

            if (!$dataType) {
                throw new Exception('Aucune donnée d\'achat direct ou d\'appel d\'offre n\'est disponible.');
            }

            $gelement = Gelement::where('reference_id', $this->notification->data['code_unique'])->first();
            if (!$gelement) {
                throw new Exception('Référence introuvable dans la table gelement.');
            }

            $valeurGelement = $gelement->amount;
            Log::info('Valeur récupérée depuis la table gelement', ['valeur' => $valeurGelement]);

            // Récupération des portefeuilles
            $fournisseurId = $this->notification->data['fournisseur'];
            $livreurId = $this->notification->data['livreur'];

            $livreurWallet = Wallet::where('user_id', $livreurId)->firstOrFail();

            // Calculs pour les montants et intérêts
            $prixTrade = $this->notification->data['prixTrade'];

            // $montantClient = $prixTrade +
            $interetLivreur = $prixTrade * 0.01;
            $montantPourLivreur = $prixTrade - $interetLivreur;

            $totalInterets = $interetLivreur;



            // Mise à jour des portefeuilles
            $livreurWallet->increment('balance', $montantPourLivreur);

            Log::info('Portefeuilles mis à jour', [
                'fournisseur_id' => $fournisseurId,
                'livreur_id' => $livreurId,
            ]);

            // Préparation des notifications
            $data = [
                'code_unique' => $this->notification->data['code_unique'],
                'achat_id' => $this->notification->data['achat_id'],
                'title' => 'Transaction réussie',
                'description' => 'Votre paiement a été traité avec succès. Merci pour votre confiance !',
            ];

            Notification::send(User::find($fournisseurId), new RefusAchat([
                'code_unique' => $this->notification->data['code_unique'],
                'id' => $this->notification->data['achat_id'],
                'title' => 'Facture Refusée',
                'description' => 'Le prix de la facture ne convient pas au client',
            ]));

            Notification::send(User::find($this->notification->data['livreur']), new Confirmation($data));
            // Création des transactions
            $this->createTransactionNew(Auth::user()->id, $livreurId, 'Réception', 'COC', $montantPourLivreur, $this->generateIntegerReference(), 'Réception pour livraison.');

            // Mise à jour de la notification
            $this->notification->update(['reponse' => 'refuser']);
            Log::info('Notification mise à jour', ['notification_id' => $this->notification->id]);
            session()->flash('succes', 'Le payement a été éffecuté avec succes.');

            DB::commit();
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Portefeuille introuvable.', ['message' => $e->getMessage()]);
            session()->flash('error', 'Un portefeuille requis est introuvable.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de l\'acceptation du colis.', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
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
        return view('livewire.mainleveclient');
    }
}

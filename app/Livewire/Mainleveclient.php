<?php

namespace App\Livewire;

use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use App\Models\ComissionAdmin;
use App\Models\gelement;
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

    public $code_verif;
    public $totalPrice;
    public $notification;
    public $id;
    public $achatdirect;
    public $appeloffre;
    public $livreur;
    public $fournisseurWallet;
    public $quantite;
    public $qualite;
    public $user;
    public $userWallet;
    public $diversite;
    public $gelement;
    public $showMainlever = false;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        $this->livreur = User::find($this->notification->data['livreur']);

        $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié
        $this->userWallet = Wallet::where('user_id', $this->user)->first();

        $this->gelement = gelement::where('reference_id', $this->notification->data['code_unique'])
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
            // Validation initiale des réponses
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

            if ($this->achatdirect == 'OffreGrouper') {
                $this->payementPlusFournisseur();
            }



            if (!$this->gelement) {
                throw new Exception('Référence introuvable dans la table gelement.');
            }

            $valeurGelement = $this->gelement->amount;

            // Récupération des portefeuilles
            $fournisseurId = $this->notification->data['fournisseur'];
            $livreurId = $this->notification->data['livreur'];


            // Calculs pour les montants et intérêts
            $prixTrade = $this->notification->data['prixTrade'];
            $interetFournisseur = ($valeurGelement - $prixTrade) * 0.01;
            $montantPourFournisseur = ($valeurGelement - $prixTrade) - $interetFournisseur;

            // $montantClient = $prixTrade +
            $interetLivreur = $prixTrade * 0.01;
            $montantPourLivreur = $prixTrade - $interetLivreur;

            $totalInterets = $interetFournisseur + $interetLivreur;



            $walletService->updateBalance($fournisseurId, $montantPourFournisseur);
            $walletService->updateBalance($livreurId, $montantPourLivreur);

            $transactionService->createTransaction(Auth::id(), $fournisseurId, 'Réception', 'COC', $montantPourFournisseur, 'Ref123', 'Paiement pour achat.');
            $transactionService->createTransaction(Auth::id(), $livreurId, 'Réception', 'COC', $montantPourLivreur, 'Ref456', 'Paiement pour livraison.');
            $transactionService->createTransaction(Auth::id(), $livreurId, 'Réception', 'COC', $montantPourLivreur, 'Ref456', 'Paiement pour livraison.');

            $commissionService->handleCommissions($totalInterets);

            $notificationService->notifyUsers([
                'code_unique' => $this->notification->data['code_unique'],
                'achat_id' => $this->notification->data['achat_id'],
                'title' => 'Transaction réussie',
                'description' => 'Votre paiement a été traité avec succès. Merci pour votre confiance !',
            ], [$fournisseurId, $livreurId]);


            // Mise à jour de la notification
            $this->notification->update(['reponse' => 'Confirmation']);
            Log::info('Notification mise à jour', ['notification_id' => $this->notification->id]);
            session()->flash('succes', 'Le payement a été éffecuté avec succes.');
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

    private function payementPlusFournisseur()
    {
        DB::beginTransaction();
        try {
            // Validate required data
            if (!$this->achatdirect || !$this->notification) {
                throw new Exception('Données requises manquantes');
            }

            // Get and validate base data
            $prixUnitaire = $this->achatdirect->prix;
            $codeUnique = $this->achatdirect->code_unique;
            $livreurId = $this->notification->data['livreur'] ?? null;
            $prixTrade = $this->notification->data['prixTrade'] ?? 0;
            $valeurGelement = $this->gelement->amount ?? 0;

            if (!$livreurId || !$prixTrade || !$valeurGelement) {
                throw new Exception('Données de paiement invalides');
            }

            // Process livreur payment
            $livreurWallet = Wallet::where('user_id', $livreurId)->firstOrFail();
            $interetLivreur = $prixTrade * 0.01;
            $montantPourLivreur = $prixTrade - $interetLivreur;
            $livreurWallet->increment('balance', $montantPourLivreur);

            $totalInterets = 0;

            Log::info('Paiement livreur effectué', [
                'livreur_id' => $livreurId,
                'montant' => $montantPourLivreur
            ]);

            // Process fournisseur payments
            $userQuantites = userquantites::where('code_unique', $codeUnique)->get();

            if ($userQuantites->isEmpty()) {
                throw new Exception('Aucune quantité utilisateur trouvée');
            }

            foreach ($userQuantites as $userQuantite) {
                $userId = $userQuantite->user_id;
                $quantite = $userQuantite->quantite;

                $prixTotal = $prixUnitaire * $quantite;
                $interetFournisseur = ($valeurGelement - $prixTotal) * 0.01;
                $montantPourFournisseur = ($valeurGelement - $prixTotal) - $interetFournisseur;

                $userWallet = Wallet::where('user_id', $userId)->firstOrFail();
                $userWallet->increment('amount', $montantPourFournisseur);
                $totalInterets += $interetFournisseur;

                // Create transactions
                $this->createTransactionNew(
                    Auth::user()->id,
                    $userId,
                    'Réception',
                    'COC',
                    $montantPourFournisseur,
                    $this->generateIntegerReference(),
                    'Réception pour achat.'
                );
            }
            // Create livreur transaction
            $this->createTransactionNew(
                Auth::user()->id,
                $livreurId,
                'Réception',
                'COC',
                $montantPourLivreur,
                $this->generateIntegerReference(),
                'Réception pour livraison.'
            );

            // Handle commissions
            $this->handleCommissions($totalInterets);

            // Update notification
            $this->notification->update(['reponse' => 'Confirmation']);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur paiement', [
                'message' => $e->getMessage(),
                'code_unique' => $codeUnique ?? null
            ]);
            throw $e;
        }
    }


    public function refuseColis()
    {

        DB::beginTransaction();
        try {
            $dataType = $this->achatdirect ? 'achatdirect' : ($this->appeloffre ? 'appeloffre' : null);

            if (!$dataType) {
                throw new Exception('Aucune donnée d\'achat direct ou d\'appel d\'offre n\'est disponible.');
            }

            $gelement = gelement::where('reference_id', $this->notification->data['code_unique'])->first();
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

            Log::info('Montants et intérêts calculés', [
                'montantPourLivreur' => $montantPourLivreur,
                'totalInterets' => $totalInterets,
            ]);

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

<?php

namespace App\Livewire;

use App\Models\AchatDirect;
use App\Models\AppelOffreUser;
use App\Models\ComissionAdmin;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
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
    public $showMainlever = false;


    public function mount($id)
    {
        $this->notification = DatabaseNotification::findOrFail($id);
        $this->achatdirect = AchatDirect::find($this->notification->data['achat_id']);
        $this->livreur = User::find($this->notification->data['livreur']);

        $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié
        $this->userWallet = Wallet::where('user_id', $this->user)->first();
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

        DB::beginTransaction();
        try {
            $dataType = $this->achatdirect ? 'achatdirect' : ($this->appeloffre ? 'appeloffre' : null);

            if (!$dataType) {
                throw new Exception('Aucune donnée d\'achat direct ou d\'appel d\'offre n\'est disponible.');
            }

            $gelement = gelement::where('reference_id', $this->notification->data['code_unique'])
                ->where('id_wallet', $this->userWallet->id)
                ->first();

            if (!$gelement) {
                throw new Exception('Référence introuvable dans la table gelement.');
            }

            $valeurGelement = $gelement->amount;
            Log::info('Valeur récupérée depuis la table gelement', ['valeur' => $valeurGelement]);

            // Récupération des portefeuilles
            $fournisseurId = $this->notification->data['fournisseur'];
            $livreurId = $this->notification->data['livreur'];

            $this->fournisseurWallet = Wallet::where('user_id', $fournisseurId)->firstOrFail();
            $livreurWallet = Wallet::where('user_id', $livreurId)->firstOrFail();

            // Calculs pour les montants et intérêts
            $prixTrade = $this->notification->data['prixTrade'];
            $interetFournisseur = ($valeurGelement - $prixTrade) * 0.01;
            $montantPourFournisseur = ($valeurGelement - $prixTrade) - $interetFournisseur;

            // $montantClient = $prixTrade +
            $interetLivreur = $prixTrade * 0.01;
            $montantPourLivreur = $prixTrade - $interetLivreur;

            $totalInterets = $interetFournisseur + $interetLivreur;

            Log::info('Montants et intérêts calculés', [
                'montantPourFournisseur' => $montantPourFournisseur,
                'montantPourLivreur' => $montantPourLivreur,
                'totalInterets' => $totalInterets,
            ]);

            // Mise à jour des portefeuilles
            $this->fournisseurWallet->increment('balance', $montantPourFournisseur);
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

            $this->notifyUsers($data, $dataType);

            // Création des transactions
            $this->createTransactionNew(Auth::user()->id, $fournisseurId, 'Réception', 'COC', $montantPourFournisseur, $this->generateIntegerReference(), 'Réception pour achat.');
            $this->createTransactionNew(Auth::user()->id, $livreurId, 'Réception', 'COC', $montantPourLivreur, $this->generateIntegerReference(), 'Réception pour livraison.');
            $this->createTransactionNew(Auth::user()->id, $fournisseurId, 'Envoie', 'COC', $valeurGelement, $this->generateIntegerReference(), 'Paiement pour achat.');

            // Gestion des commissions
            $this->handleCommissions($totalInterets);

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

    private function notifyUsers(array $data, string $dataType)
    {
        $fournisseurId = $dataType === 'achatdirect' ? $this->achatdirect->userTrader : $this->appeloffre->user_id;

        Notification::send(User::find(Auth::id()), new Confirmation($data));
        Notification::send(User::find($fournisseurId), new Confirmation($data));
        Notification::send(User::find($this->notification->data['livreur']), new Confirmation($data));

        Log::info('Notifications envoyées.', [
            'user_id' => Auth::id(),
            'fournisseur_id' => $fournisseurId,
            'livreur_id' => $this->notification->data['livreur'],
        ]);
    }


    private function handleCommissions(float $totalInterets)
    {
        $commissions = $totalInterets - ($totalInterets * 0.01);

        if ($this->fournisseurWallet->user->parrain) {
            $commissions = $this->distributeToParrains($commissions);
        }

        $this->distributeToAdmin($commissions);
    }
    private function distributeToParrains(float $commissions)
    {
        $parrain = $this->fournisseurWallet->user->parrain;
        $level = 1;

        while ($parrain && $level <= 3) {
            $parrainUser = User::find($parrain);
            $parrainWallet = Wallet::where('user_id', $parrainUser->id)->first();

            if ($parrainWallet) {
                $commissionForParrain = $commissions * 0.01;
                $parrainWallet->increment('balance', $commissionForParrain);
                $commissions -= $commissionForParrain;

                Log::info("Commission envoyée au parrain niveau $level", ['parrain_id' => $parrainUser->id, 'commission' => $commissionForParrain]);

                $this->createTransaction(Auth::user()->id, $parrainUser->id, 'Commission', $commissionForParrain, $this->generateIntegerReference(), "Commission niveau $level", 'effectué', 'COC');
            }

            $parrain = $parrainUser->parrain;
            $level++;
        }

        return $commissions;
    }

    private function distributeToAdmin(float $commissions)
    {
        $adminWallet = ComissionAdmin::where('admin_id', 1)->first();
        if ($adminWallet) {
            $adminWallet->increment('balance', $commissions);

            Log::info('Commission envoyée à l\'admin.', ['admin_id' => 1, 'commissions' => $commissions]);

            $this->createTransactionAdmin(
                Auth::user()->id,
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

    protected function createTransactionNew(int $senderId, int $receiverId, string $type, string $type_compte, float $amount, int $reference_id, string $description)
    {

        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->type_compte = $type_compte;
        $transaction->amount = $amount;
        $transaction->reference_id = $reference_id;
        $transaction->description = $description;
        $transaction->status = 'effectué';
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
        return view('livewire.mainleveclient');
    }
}

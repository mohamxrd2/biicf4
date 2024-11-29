<?php

namespace App\Livewire;

use App\Models\AppelOffreUser;
use App\Models\ComissionAdmin;
use App\Models\gelement;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\commandVerifAp;
use App\Notifications\Confirmation;
use App\Notifications\mainleveAd;
use App\Notifications\mainleveAp;
use App\Notifications\RefusAchat;
use App\Notifications\VerifUser;
use Exception;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;

class CountdownNotificationAp extends Component
{
    public $notification;
    public $id;
    public $produit;
    public $userFour;
    public $totalPrice;
    public $user;
    public $appeloffre;
    public $livreur;
    public $codeVerification;
    public $fournisseur;
    public $userWallet;



    public function mount($id)
    {
        try {
            $this->notification = DatabaseNotification::findOrFail($id);
            $this->userFour = User::find($this->notification->data['fournisseur'] ?? null);
            $this->user = Auth::id(); // Initialisation de $user avec l'utilisateur authentifié
            $this->appeloffre = AppelOffreUser::find($this->notification->data['id_appeloffre']);
            $this->codeVerification = $this->appeloffre->code_verification;
            $prodUsers = json_decode($this->appeloffre->prodUsers, true); // Décodage en tableau associatif
            $this->produit = ProduitService::where('reference', $this->appeloffre->reference)
                ->where('user_id', $prodUsers)
                ->first();

            if (!$this->appeloffre) {
                throw new Exception("AchatDirect introuvable avec l'ID fourni.");
            }

            $this->fournisseur = User::find($this->produit->user_id);
            $this->livreur = User::find($this->notification->data['livreur']);

            if (!$this->produit) {
                throw new Exception("ProduitService introuvable avec l'ID fourni.");
            }

            $this->userWallet = Wallet::where('user_id', $this->user)->first();

            if (!$this->userWallet) {
                Log::error('Portefeuille introuvable pour l\'utilisateur', ['userId' => $this->user]);
                session()->flash('error', 'Portefeuille introuvable.');
                return;
            }
            Log::info('Portefeuille trouvé', ['userWallet' => $this->userWallet]);
        } catch (Exception $e) {
            Log::error("Erreur dans mount : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de la récupération des données.');
        }
    }

    public function FactureRefuser()
    {
        try {
            // Vérification des données récupérées
            if (!$this->appeloffre) {
                throw new Exception("Données introuvables pour le livreur ou l'achat.");
            }

            $livreurdetails = [
                'id_appeloffre' => $this->appeloffre->id,
                'idProd' => $this->produit->id,
                'code_unique' => $this->notification->data['code_unique'],
                'title' => 'Facture Refusée',
                'description' => 'Le prix de la facture ne convient pas au client',
            ];

            // Envoi de la notification au fournisseur
            if ($this->fournisseur) {
                Notification::send($this->fournisseur, new RefusAchat($livreurdetails));
            } else {
                Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->appeloffre->id);
            }

            // Envoi de la notification au livreur
            if ($this->livreur) {
                Notification::send($this->livreur, new Confirmation($livreurdetails));
            } else {
                Log::warning("Livreur introuvable pour la notification ID : " . $this->notification->id);
            }

            // Mise à jour de l'état de la notification
            $this->notification->update(['reponse' => 'refuser']);
            session()->flash('success', 'La facture a été refusée et les notifications ont été envoyées.');
        } catch (Exception $e) {
            Log::error("Erreur dans FactureRefuser : " . $e->getMessage());
            session()->flash('error', 'Une erreur s\'est produite lors de l\'envoi des notifications.');
        }
    }

    public function valider()
    {
        DB::beginTransaction();
        try {
            if ($this->notification->type_achat == 'Delivery') {
                $this->pour_livraison();
            } elseif ($this->notification->type_achat == 'Take Away') {
                $this->retait_magasin();
            }
            // Générer et stocker le code de vérification
            $this->codeVerification = random_int(1000, 9999);
            $this->appeloffre->update([
                'code_verification' => $this->codeVerification,
            ]);

            // Préparer les données pour le fournisseur
            $dataFournisseur = [
                'code_unique' => $this->appeloffre->code_unique,
                'CodeVerification' => $this->codeVerification, // Utilisez cette propriété
                'client' => $this->appeloffre->userSender,
                'id_appeloffre' => $this->appeloffre->id,
            ];

            Notification::send($this->fournisseur, new VerifUser($dataFournisseur));

            // Mettre à jour la notification et valider
            $this->notification->update(['reponse' => 'accepter']);
            Log::info('Notification mise à jour', ['notificationId' => $this->notification->id]);
            DB::commit();

            session()->flash('success', 'Validation effectuée avec succès.');
        } catch (Exception $e) {
            DB::rollback();
            // Gérer les exceptions générales
            Log::error('Erreur lors de la validation', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            session()->flash('error', 'Une erreur est survenue lors du processus de validation. Veuillez réessayer.');
        }
    }
    private function pour_livraison()
    {
        // Calcul du montant requis
        $requiredAmount = floatval($this->notification->data['prixTrade']);

        // Vérification des fonds disponibles
        if ($this->userWallet->balance < $requiredAmount) {
            Log::error('Fonds insuffisants pour l\'achat', [
                'balance' => $this->userWallet->balance,
                'requiredAmount' => $requiredAmount
            ]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        // Vérification de l'existence de l'achat dans les transactions gelées
        $existingGelement = gelement::where('reference_id', $this->notification->data['code_unique'])
            ->first();

        if (!$existingGelement) {
            Log::warning('Aucune transaction gelée existante trouvée.', [
                'reference_id' => $this->notification->data['code_unique']
            ]);
            return; // Arrête la fonction si aucune transaction n'est trouvée
        }

        DB::beginTransaction(); // Démarre une transaction pour garantir la cohérence
        try {
            // Débit du portefeuille utilisateur
            $this->userWallet->balance -= $requiredAmount;
            $this->userWallet->save();

            Log::info('Portefeuille débité avec succès.', [
                'user_id' => $this->userWallet->user_id,
                'new_balance' => $this->userWallet->balance,
                'debited_amount' => $requiredAmount
            ]);

            // Mise à jour du montant gelé
            $existingGelement->amount += $requiredAmount;
            $existingGelement->save();

            Log::info('Montant ajouté à une transaction existante', [
                'transaction_id' => $existingGelement->id,
                'new_amount' => $existingGelement->amount
            ]);

            // Création de la transaction
            $this->createTransaction(
                $this->user,
                $this->user,
                'Gele',
                $requiredAmount,
                $this->generateIntegerReference(),
                'Montant gelé pour la livraison',
                'effectué',
                'COC'
            );

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

    private function retait_magasin()
    {
        // Calcul du montant requis avec une réduction de 1% cest pour le retrait en magasin
        $requiredAmount = floatval($this->notification->data['prixFin']);

        if ($this->userWallet->balance < $requiredAmount) {
            Log::error('Fonds insuffisants pour l\'achat', ['balance' => $this->userWallet->balance, 'requiredAmount' => $requiredAmount]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        $this->createTransaction(
            $this->user,
            $this->fournisseur->id ?? null,
            'Envoie',
            $requiredAmount,
            $this->generateIntegerReference(),
            'Debité pour achat',
            'effectué',
            'COC'
        );

        $roi = $this->appeloffre->montantTotal * 0.01 / 100;
        $commissions = $roi - $roi * 0.01;

        if ($this->fournisseur->parrain) {

            $parrainLevel1 = User::find($this->fournisseur->parrain);
            $parrainLevel1Wallet = Wallet::where('user_id', $parrainLevel1->id)->first();
            if ($parrainLevel1Wallet) {
                $parrainLevel1Wallet->balance += $commissions * 0.01;
                $parrainLevel1Wallet->save();

                Log::info('Commission envoyée au parrain', [
                    'parrain_id' => $parrainLevel1->id,
                    'commissions' => $commissions * 0.01,
                ]);

                $this->createTransaction(
                    $this->user,
                    $parrainLevel1->id,
                    'Commission',
                    $commissions * 0.01,
                    $this->generateIntegerReference(),
                    'Commission de BICF',
                    'effectué',
                    'COC'
                );

                $commissions -= $commissions * 0.01;
            }




            if ($parrainLevel1->parrain) {

                $parrainLevel2 = User::find($parrainLevel1->parrain);
                $parrainLevel2Wallet = Wallet::where('user_id', $parrainLevel2->id)->first();

                if ($parrainLevel2Wallet) {
                    $parrainLevel2Wallet->balance += $commissions * 0.01;
                    $parrainLevel2Wallet->save();

                    // Log de la mise à jour
                    Log::info('Commission envoyée au deuxième parrain', [
                        'parrain_id' => $parrainLevel2->id,
                        'commissions' => $commissions * 0.01
                    ]);

                    // Créer une transaction vers le deuxième parrain
                    $this->createTransaction(
                        $this->user,
                        $parrainLevel2->id,
                        'Commission',
                        $commissions * 0.01,
                        $this->generateIntegerReference(),
                        'Commission de BICF',
                        'effectué',
                        'COC'
                    );

                    $commissions = $commissions - $commissions * 0.01;
                }

                if ($parrainLevel2->parrain) {
                    $parrainLevel3 = User::find($parrainLevel2->parrain);
                    $parrainLevel3Wallet = Wallet::where('user_id', $parrainLevel3->id)->first();
                    if ($parrainLevel3Wallet) {
                        $parrainLevel3Wallet->balance += $commissions * 0.01;
                        $parrainLevel3Wallet->save();

                        // Log de la mise à jour
                        Log::info('Commission envoyée au troisième parrain', [
                            'parrain_id' => $parrainLevel3->id,
                            'commissions' => $commissions * 0.01
                        ]);

                        // Créer une transaction vers le troisième parrain
                        $this->createTransaction(
                            $this->user,
                            $parrainLevel3->id,
                            'Commission',
                            $commissions * 0.01,
                            $this->generateIntegerReference(),
                            'Commission de BICF',
                            'effectué',
                            'COC'
                        );

                        $commissions = $commissions - $commissions * 0.01;
                    }
                }
            }
        }

        // Envoyé commission a l'admin

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
    public $quantite;
    public $qualite;
    public $diversite;




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

            if (!$this->livreur) {

                // Préparer les données pour le fournisseur
                $dataFournisseur = [
                    'code_unique' => $this->appeloffre->code_unique,
                    'id_appeloffre' => $this->appeloffre->id ?? null,
                    'title' => 'Commande récupérée avec succès',
                    'description' => 'Votre commande a été récupérée avec succès. Merci de votre confiance !',
                ];

                if ($this->fournisseur) {
                    Notification::send($this->fournisseur, new Confirmation($dataFournisseur));
                    Log::info('Notification envoyée au fournisseur', ['fournisseurId' => $this->fournisseur->id, 'code' => $fournisseurCode]);
                } else {
                    Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->appeloffre->id);
                }
            } else {
                // Préparer les données pour le fournisseur
                $dataFournisseur = [
                    'code_unique' => $this->appeloffre->code_unique,
                    'fournisseurCode' => $fournisseurCode,
                    'livreurCode' => $livreurCode,
                    'livreur' => $this->notification->data['livreur'] ?? null,
                    'client' => $this->appeloffre->userSender ?? null,
                    'id_appeloffre' => $this->appeloffre->id ?? null,
                    'title' => 'Recuperation de la commande',
                    'description' => 'Remettez le colis au livreur->',
                ];

                if ($this->fournisseur) {
                    Notification::send($this->fournisseur, new mainleveAp($dataFournisseur));
                    Log::info('Notification envoyée au fournisseur', ['fournisseurId' => $this->fournisseur->id, 'code' => $fournisseurCode]);
                } else {
                    Log::warning("Fournisseur introuvable pour l'achat direct ID : " . $this->appeloffre->id);
                }

                // Préparer les données pour le livreur
                $dataLivreur = [
                    'code_unique' => $this->appeloffre->code_unique,
                    'livreurCode' => $livreurCode,
                    'fournisseurCode' => $fournisseurCode,
                    'fournisseur' => $this->fournisseur->id ?? null,
                    'client' => $this->appeloffre->userSender ?? null,
                    'id_appeloffre' => $this->appeloffre->id ?? null,
                    'prixTrade' => $this->notification->data['prixTrade'] ?? null,
                    'title' => 'Livraison a effectuer',
                    'description' => 'Deplacez vous pour aller chercher le colis->',
                ];
                if ($this->livreur) {
                    Notification::send($this->livreur, new mainleveAp($dataLivreur));
                    Log::info('Notification envoyée au livreur', ['livreurId' => $this->livreur->id, 'code' => $livreurCode]);
                } else {
                    Log::warning("Livreur introuvable pour la notification ID : " . $this->notification->id);
                }
            }
            $this->notification->update(['reponse' => 'mainleveclient']);

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
        return view('livewire.countdown-notification-ap');
    }
}

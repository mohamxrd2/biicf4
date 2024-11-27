<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AppelOffreGrouper;
use App\Models\AppelOffreUser;
use App\Models\Consommation;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\User;
use App\Models\userquantites;
use App\Notifications\AOGrouper;
use App\Notifications\AppelOffre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Str;

class Appaeloffre extends Component
{

    public $wallet;
    public $lowestPricedProduct;
    public $distinctCondProds;
    public $type;
    public $prodUsers = [];
    public $distinctquatiteMax;
    public $distinctquatiteMin;
    public $name;
    public $reference;
    public $distinctSpecifications = [];
    public $appliedZoneValue;
    public $quantité;
    public $localite;
    public $selectedOption;
    public $dateTot;
    public $dateTard;
    public $timeStart;
    public $timeEnd;
    public $dayPeriod;
    public $dayPeriodFin;
    public $loading = false;

    public function mount(
        $wallet,
        $lowestPricedProduct,
        $distinctCondProds,
        $type,
        $prodUsers,
        $distinctquatiteMax,
        $distinctquatiteMin,
        $name,
        $reference,
        $distinctSpecifications,
        $appliedZoneValue
    ) {
        $this->wallet = $wallet;
        $this->lowestPricedProduct = $lowestPricedProduct;
        $this->distinctCondProds = $distinctCondProds;
        $this->type = $type;
        $this->prodUsers = $prodUsers;
        $this->distinctquatiteMax = $distinctquatiteMax;
        $this->distinctquatiteMin = $distinctquatiteMin;
        $this->name = $name;
        $this->reference = $reference;
        $this->distinctSpecifications = implode(', ', $distinctSpecifications);
        $this->appliedZoneValue = $appliedZoneValue;
    }

    public function submitEnvoie()
    {
        $this->loading = true;
        $userId = Auth::guard('web')->id();

        DB::beginTransaction();
        try {
            // Validation des données du formulaire
            $validatedData = $this->validate([
                'name' => 'required|string',
                'reference' => 'required|string',
                'quantité' => 'required|integer',
                'localite' => 'required|string',
                'distinctSpecifications' => 'required|string',
                'selectedOption' => 'required|string',
                'dateTot' => $this->selectedOption == 'required|date',
                'dateTard' => $this->selectedOption == 'required|date',
                'timeStart' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
                'timeEnd' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
                'dayPeriod' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
                'dayPeriodFin' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
                'lowestPricedProduct' => 'required|integer',
                'prodUsers' => 'required|array',
            ]);

            // Insérer dans la table `appel_offres`
            $appelOffre = AppelOffreUser::create([
                'product_name' => $validatedData['name'],
                'quantity' => $validatedData['quantité'],
                'payment' => 'comptant',
                'livraison' => $validatedData['selectedOption'],
                'date_tot' => $validatedData['dateTot'],
                'date_tard' => $validatedData['dateTard'],
                'time_start' => $validatedData['timeStart'],
                'time_end' => $validatedData['timeEnd'],
                'day_period' => $validatedData['dayPeriod'],
                'day_periodFin' => $validatedData['dayPeriodFin'],
                'specification' => $validatedData['distinctSpecifications'],
                'reference' => $validatedData['reference'],
                'localite' => $validatedData['localite'],
                'code_unique' => $this->generateUniqueReference(),
                'lowestPricedProduct' => $validatedData['lowestPricedProduct'],
                'prodUsers' => json_encode($validatedData['prodUsers']),
                'image' => $validatedData['image'] ?? null, // Valeur par défaut si aucune image n'est fournie
                'id_sender' => Auth::id(), // L'utilisateur connecté est le créateur
            ]);

            // Calculer le coût total
            $totalCost = $this->quantité * $this->lowestPricedProduct;

            // Vérification du solde avant décrémentation
            if ($this->wallet->balance < $totalCost) {
                throw new \Exception("Solde insuffisant pour effectuer cette transaction.");
            }

            // Décrémenter le solde du portefeuille
            $this->wallet->decrement('balance', $totalCost);
            // Créer  transactions
            $this->createTransaction($userId, $userId, 'Gele', $totalCost, $this->generateIntegerReference(), 'Gele Pour ' . 'Achat de ' . $this->name, 'effectué', 'COC');
            // Mettre à jour la table de gelement de fond
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $totalCost,
                'reference_id' => $this->generateIntegerReference(),
            ]);

            // Convertir les utilisateurs cibles en JSON si nécessaire (dans votre exemple, prodUsers est encodé)
            $prodUsers =  $validatedData['prodUsers']; // Convertit le JSON en tableau
            foreach ($prodUsers as $prodUser) {
                $data = [
                    'id_appelOffre' => $appelOffre->id,
                    'code_unique' => $appelOffre->code_unique,
                    'difference' => 'single',
                ];
                // Récupération de l'utilisateur destinataire
                $owner = User::find($prodUser);

                // Vérification si l'utilisateur existe
                if ($owner) {
                    // Envoi de la notification à l'utilisateur
                    Notification::send($owner, new AppelOffre($data));
                    // Déclencher un événement pour signaler l'envoi de la notification
                    event(new NotificationSent($owner));

                    // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
                    $notification = $owner->notifications()->where('type', AppelOffre::class)->latest()->first();

                    if ($notification) {
                        // Mettez à jour le champ 'type_achat' dans la notification
                        $notification->update(['type_achat' => $this->selectedOption]);
                    }
                }
            }
            // Redirection ou traitement pour l'envoi direct
            $this->dispatch(
                'formSubmitted',
                'Demande d\'appel offre a ette effectué avec succes.'
            );
            DB::commit();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Vous pouvez gérer des erreurs personnalisées ici si nécessaire
            $this->addError('formError', 'Une erreur est survenue pendant la validation.');
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

    public function submitGroupe()
    {
        Log::info('Début du processus de création de l\'appel d\'offre groupé.');

        // Vérifier si la zone économique est sélectionnée
        if (!$this->appliedZoneValue) {
            Log::warning('Aucune zone économique sélectionnée par l\'utilisateur.');
            session()->flash('error', 'Veuillez sélectionner une zone économique pour pouvoir vous grouper.');
            return;
        }

        $this->loading = true;
        $userId = Auth::guard('web')->id(); // Récupérer l'ID de l'utilisateur connecté
        Log::info('Utilisateur connecté.', ['user_id' => $userId]);

        DB::beginTransaction(); // Démarrer une transaction

        try {
            // Valider les données
            Log::info('Validation des données en cours.');
            $validatedData = $this->validate([
                'name' => 'required|string|max:255',
                'quantité' => 'required|integer|min:1',
                'selectedOption' => 'required|string|max:255',
                'dateTot' => 'required|date|before_or_equal:dateTard',
                'dateTard' => 'required|date|after_or_equal:dateTot',
                'distinctSpecifications' => 'required|string|max:500',
                'localite' => 'required|string|max:255',
                'appliedZoneValue' => 'required|string|max:255',
                'prodUsers' => 'required|array|min:1',
            ]);
            Log::info('Données validées avec succès.', ['validated_data' => $validatedData]);

            // Créer une instance pour l'appel d'offre groupé
            Log::info('Création de l\'appel d\'offre groupé.');

            // Insérer dans la table `appel_offres`
                $offre = AppelOffreGrouper::create([
                'lowestPricedProduct' => $this->lowestPricedProduct,
                'productName' => $validatedData['name'],
                'quantity' => $validatedData['quantité'],
                'payment' => 'comptant',
                'Livraison' => $validatedData['selectedOption'],
                'dateTot' => $validatedData['dateTot'],
                'dateTard' => $validatedData['dateTard'],
                'specificity' => $validatedData['distinctSpecifications'],
                'localite' => $validatedData['localite'],
                'image' => $validatedData['image'] ?? null, // Valeur par défaut si aucune image n'est fournie
                'prodUsers' => json_encode($validatedData['prodUsers']),
                'codeunique' => $this->generateUniqueReference(),
                'user_id' => $userId,
            ]);
            Log::info('Appel d\'offre groupé enregistré.', ['offre_id' => $offre->id]);

            // Enregistrer la quantité utilisateur
            Log::info('Enregistrement de la quantité utilisateur.');
            $quantite = new userquantites();
            $quantite->user_id = $userId;
            $quantite->localite = $validatedData['localite'];
            $quantite->type_achat = $validatedData['selectedOption'];
            $quantite->quantite = $validatedData['quantité'];
            $quantite->code_unique = $this->generateUniqueReference();
            $quantite->save();
            Log::info('Quantité utilisateur enregistrée.', ['quantite_id' => $quantite->id]);

            // Calcul du coût total
            $totalCost = $this->quantité * $this->lowestPricedProduct;
            Log::info('Coût total calculé.', ['total_cost' => $totalCost]);

            // Vérifier le solde du portefeuille
            if ($this->wallet->balance < $totalCost) {
                Log::error('Solde insuffisant.', ['balance' => $this->wallet->balance, 'total_cost' => $totalCost]);
                throw new \Exception("Solde insuffisant pour effectuer cette transaction.");
            }

            // Décrémenter le solde du portefeuille
            $this->wallet->decrement('balance', $totalCost);
            Log::info('Solde du portefeuille décrémenté.', ['new_balance' => $this->wallet->balance]);

            // Enregistrer la transaction
            $this->createTransaction(
                $userId,
                $userId,
                'Gele',
                $totalCost,
                $this->generateIntegerReference(),
                'Gele pour achat de ' . $validatedData['name'],
                'effectué',
                'COC'
            );
            Log::info('Transaction enregistrée.', ['amount' => $totalCost]);

            // Enregistrer le gel des fonds
            gelement::create([
                'id_wallet' => $this->wallet->id,
                'amount' => $totalCost,
                'reference_id' => $this->generateIntegerReference(),
            ]);
            Log::info('Gel des fonds enregistré.', ['amount' => $totalCost]);

            // Récupérer les IDs des utilisateurs potentiellement intéressés
            $idsProprietaires = Consommation::where('name', $offre->productName)
                ->where('id_user', '!=', $userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();
            Log::info('IDs des propriétaires récupérés.', ['ids_proprietaires' => $idsProprietaires]);

            // Récupérer les IDs d'utilisateurs dans la même localité
            $idsLocalite = User::whereIn('id', $idsProprietaires)
                ->where(function ($query) use ($validatedData) {
                    $query->where('continent', $validatedData['appliedZoneValue'])
                        ->orWhere('sous_region', $validatedData['appliedZoneValue'])
                        ->orWhere('country', $validatedData['appliedZoneValue'])
                        ->orWhere('departe', $validatedData['appliedZoneValue'])
                        ->orWhere('ville', $validatedData['appliedZoneValue'])
                        ->orWhere('commune', $validatedData['appliedZoneValue']);
                })
                ->pluck('id')
                ->toArray();
            Log::info('IDs des utilisateurs dans la même localité récupérés.', ['ids_localite' => $idsLocalite]);

            // Fusionner les IDs uniques
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            Log::info('IDs à notifier fusionnés.', ['ids_to_notify' => $idsToNotify]);

            if (empty($idsToNotify)) {
                throw new \Exception('Aucun utilisateur ne consomme ce produit dans votre zone économique.');
            }

            // Envoyer les notifications
            foreach ($idsToNotify as $id) {
                $user = User::find($id);
                if ($user) {
                    Notification::send($user, new AOGrouper($offre->codeunique, $offre->id));
                    Log::info('Notification envoyée.', ['user_id' => $id, 'offre_id' => $offre->id]);
                }
            }

            DB::commit(); // Valider la transaction
            Log::info('Transaction DB validée.');
            session()->flash('success', 'Appel d\'offre groupé créé avec succès.');
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction
            Log::error('Erreur dans le regroupement.', ['error' => $e->getMessage()]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }

        // Redirection ou mise à jour d'état
        $this->dispatch('formSubmitted', 'groupe');
        Log::info('Fin du processus de création de l\'appel d\'offre groupé.');
    }


    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
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
        return view('livewire.appaeloffre');
    }
}
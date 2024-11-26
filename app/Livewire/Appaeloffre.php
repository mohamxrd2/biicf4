<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AppelOffreUser;
use App\Models\gelement;
use App\Models\Transaction;
use App\Models\User;
use App\Notifications\AppelOffre;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
                'dateTot' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
                'dateTard' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
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
    public function submitGroupe()
    {
        if (!$this->appliedZoneValue) {
            session()->flash('error', 'Veuillez sélectionner une zone économique pour pouvoir vous grouper.');
            return;
        }

        $this->loading = true;
        // Redirection ou traitement pour le regroupement
        $this->dispatch(
            'formSubmitted',
            'groupe.'
        );
    }

    public function render()
    {
        return view('livewire.appaeloffre');
    }
}

<?php

namespace App\Livewire;

use App\Events\NotificationSent;
use App\Models\AchatDirect;
use App\Models\ProduitService;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\AchatBiicf;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Livewire\Component;
use Illuminate\Support\Str;

class Offrenegosterminer extends Component
{
    public $notification;
    public $id;
    public $produitId;
    public $produit;
    public $userId;
    public $prixProd;
    public $quantité;
    public $quantite;
    public $type;
    public $idProd;
    public $photo;
    public $selectedOption = "";
    public $options = [
        'Achat avec livraison',
        'Take Away',
        'Reservation',
    ];
    public $optionsC = [
        'Take Away',
        'Reservation',
    ];
    //
    public $nameProd;
    public $localite;
    public $userTrader;
    public $selectedSpec = false;
    public $message = "Un utilisateur veut acheter ce produit";
    public $code_unique;
    public $dateTard;
    public $dateTot;
    public $timeStart;
    public $timeEnd;
    public $dayPeriod = "";

    public function mount($id)
    {

        $this->notification = DatabaseNotification::findOrFail($id);

        $this->produit = ProduitService::findOrFail($this->notification->data['idProd']);
       
    }
    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }

    public function AchatDirectForm()
    {
        $validated = $this->validate([
            'quantité' => 'required|integer',
            'localite' => 'required|string|max:255',
            'selectedOption' => 'required|string',
            'dateTot' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'dateTard' => $this->selectedOption == 'Take Away' ? 'required|date' : 'nullable|date',
            'timeStart' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'timeEnd' => $this->selectedOption == 'Take Away' ? 'nullable|date_format:H:i' : 'nullable|date_format:H:i',
            'dayPeriod' => $this->selectedOption == 'Take Away' ? 'nullable|string' : 'nullable|string',
            'nameProd' => 'required|string',
            'userTrader' => 'required|exists:users,id',
            'prixProd' => 'required|numeric',
            'idProd' => 'required|exists:produit_services,id',
        ]);

        // dd($validated);

        Log::info('Validation réussie.', $validated);

        $userId = Auth::id();
        $montantTotal = $validated['quantité'] * $validated['prixProd'];

        if (!$userId) {
            Log::error('Utilisateur non authentifié.');
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            Log::error('Portefeuille introuvable.', ['userId' => $userId]);
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        if ($userWallet->balance < $montantTotal) {
            Log::warning('Fonds insuffisants pour effectuer cet achat.', [
                'userId' => $userId,
                'requiredAmount' => $montantTotal,
                'walletBalance' => $userWallet->balance,
            ]);
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            // Utilisez `selectedSpec` pour obtenir la spécification sélectionnée
            $selectedSpec = $this->selectedSpec;

            // Assurez-vous que `selectedSpec` est bien défini
            $specificites = !empty($selectedSpec) ? $selectedSpec : null;

            $achat = AchatDirect::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montantTotal,
                'localite' => $validated['localite'],
                'date_tot' => $validated['dateTot'],
                'date_tard' => $validated['dateTard'],
                'timeStart' => $validated['timeStart'],
                'timeEnd' => $validated['timeEnd'],
                'dayPeriod' => $validated['dayPeriod'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $userId,
                'specificite' => $specificites,
                'photoProd' => $this->photo,
                'idProd' => $validated['idProd'],
                'code_unique' => $this->code_unique,

            ]);
            // dd($achat);
            $userWallet->decrement('balance', $montantTotal);

            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montantTotal;
            $transaction->save();

            Log::info('Transaction enregistrée.', [
                'transactionId' => $transaction->id,
                'amount' => $montantTotal,
            ]);

            $owner = User::find($validated['userTrader']);
            $selectedOption = $this->selectedOption;
            Notification::send($owner, new AchatBiicf($achat));
            // Après l'envoi de la notification
            event(new NotificationSent($owner));

            // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
            $notification = $owner->notifications()->where('type', AchatBiicf::class)->latest()->first();

            if ($notification) {
                // Mettez à jour le champ 'type_achat' dans la notification
                $notification->update(['type_achat' => $selectedOption]);
            }

            $user = User::find($userId);
            $this->reset(['quantité', 'localite', 'selectedSpec']);
            session()->flash('success', 'Achat passé avec succès.');
            // Émettre un événement après la soumission
            $this->dispatch('formSubmitted', 'achat effectué avec success');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'achat direct.', [
                'error' => $e->getMessage(),
                'userId' => $userId,
                'data' => $validated,
            ]);
            session()->flash('error', 'Une erreur est survenue : ' . $e->getMessage());
        }
    }



    public function render()
    {
        // Récupérer le produit ou échouer
        $produit = ProduitService::findOrFail($this->notification->data['idProd']);

        // Récupérer l'identifiant de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer le portefeuille de l'utilisateur
        $userWallet = Wallet::where('user_id', $userId)->first();


        return view(
            'livewire.offrenegosterminer',
            compact(
                'produit',
                'userWallet',
                'userId'
            )
        );
    }
}

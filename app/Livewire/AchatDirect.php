<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Notification;
use App\Models\AchatDirect as AchatDirectModel;
use App\Models\User;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Events\MyEvent;
use App\Notifications\AchatBiicf;

class AchatDirect extends Component
{
    public $id;
    public $produit;
    public $userId;
    //
    public $quantité = "";
    public $localite = "";
    public $specificite = "";
    public $userTrader;
    public $nameProd;
    public $userSender;
    public $message = "Un utilisateur veut acheter ce produit";
    public $photoProd;
    public $idProd;
    public $prix;

    protected $rules = [
        'nameProd' => 'required|string',
        'quantité' => 'required|integer',
        'prix' => 'required|numeric',
        'localite' => 'required|string|max:255',
        'userTrader' => 'required|exists:users,id',
        'userSender' => 'required|exists:users,id',
        'photoProd' => 'required|string',
        'idProd' => 'required|exists:produit_services,id',
    ];

    public function mount($id)
    {
        $this->id = $id;
        $this->produit = ProduitService::findOrFail($id);
        $this->userId = Auth::guard('web')->id();
        $this->nameProd = $this->produit->name;
        $this->userSender = $this->userId;
        $this->userTrader = $this->produit->user->id;
        $this->photoProd = $this->produit->photoProd1;
        $this->idProd = $this->produit->id;
        $this->prix = $this->produit->prix;
    }
    public function submitForm()
    {
        $validated = $this->validate();

        $userId = Auth::id();

        $montanTotal = $validated['quantité'] * $validated['prix'];

        if (!$userId) {
            session()->flash('error', 'Utilisateur non authentifié.');
            return;
        }

        $userWallet = Wallet::where('user_id', $userId)->first();

        if (!$userWallet) {
            session()->flash('error', 'Portefeuille introuvable.');
            return;
        }

        $requiredAmount = $montanTotal;

        if ($userWallet->balance < $requiredAmount) {
            session()->flash('error', 'Fonds insuffisants pour effectuer cet achat.');
            return;
        }

        try {
            $achat = AchatDirectModel::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $montanTotal,
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $this->specificite,
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
            ]);

            $userWallet->decrement('balance', $requiredAmount);

            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $montanTotal;
            $transaction->save();

            $owner = User::find($validated['userTrader']);
            Notification::send($owner, new AchatBiicf($achat));

            $user = User::find($userId);
            event(new MyEvent($user));

            session()->flash('success', 'Achat passé avec succès.');
        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }
    public function render()
    {

        return view('livewire.achat-direct', [
            'id' => $this->id,
            'produit' => $this->produit,
            'userId' => $this->userId, // Passer l'ID de l'utilisateur connecté à la vue
        ]);
    }
}

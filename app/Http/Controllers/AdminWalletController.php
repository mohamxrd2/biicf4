<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminWalletController extends Controller
{
    //
    public function index()
    {
        return view('admin.wallet');
    }



    public function sendToClientAccount(Request $request)
    {
        // Validate request data
        $validatedData = $request->validate(
            [
                'user_id' => 'required',
                'amount' => 'required|numeric',
            ],
            [
                'user_id.required' => 'Veuillez sélectionner un client.',
                'amount.required' => 'Veuillez entrer le montant.',
            ]
        );

        // Calculate commission
        $pourcentSomme = $request->input('comAmount');
        $senderComm = $pourcentSomme * 0.01;

        // Find the user specified in the request
        $user = User::find($validatedData['user_id']);
        if (!$user) {
            return redirect()->back()->with('error', 'L\'utilisateur spécifié n\'existe pas.');
        }

        // Get the authenticated user
        $userId = Auth::guard('web')->id();
        $authenticatedUser = User::find($userId);
        if (!$authenticatedUser) {
            return redirect()->back()->with('error', 'Utilisateur authentifié non trouvé.');
        }

        // Calculate parrain commissions and find their wallets if applicable
        $senderParranCom = 0;
        $senderParranWallet = null;
        if ($authenticatedUser->parrain) {
            $senderParranCom = $pourcentSomme * 0.01;
            $senderParranWallet = Wallet::where('user_id', $authenticatedUser->parrain)->first();
        }

        $receiveParrainCom = 0;
        $receiveParrainWallet = null;
        if ($user->parrain) {
            $receiveParrainCom = $pourcentSomme * 0.01;
            $receiveParrainWallet = Wallet::where('user_id', $user->parrain)->first();
        }

        // Find the wallets
        $userReceiveWallet = Wallet::where('user_id', $user->id)->first();
        $userSenderWallet = Wallet::where('user_id', $userId)->first();
        if (!$userReceiveWallet || !$userSenderWallet) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération des portefeuilles.');
        }

        // Check sender's balance
        if ($userSenderWallet->balance < $validatedData['amount']) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer la recharge.');
        }

        // Update wallet balances
        $userReceiveWallet->increment('balance', $validatedData['amount']);
        $userSenderWallet->decrement('balance', $validatedData['amount']);
        $userSenderWallet->increment('balance', $senderComm);

        if ($senderParranWallet) {
            $senderParranWallet->increment('balance', $senderParranCom);
        }
        if ($receiveParrainWallet) {
            $receiveParrainWallet->increment('balance', $receiveParrainCom);
        }

        // Create transactions
        $this->createTransaction($userId, $userId, 'Commission', $senderComm);
        $this->createTransaction($userId, $user->id, 'Reception', $validatedData['amount']);
        $this->createTransaction($userId, $user->id, 'Envoie', $validatedData['amount']);

        if ($senderParranWallet) {
            $this->createTransaction($userId, $authenticatedUser->parrain, 'Commission', $senderParranCom);
        }
        if ($receiveParrainWallet) {
            $this->createTransaction($userId, $user->parrain, 'Commission', $receiveParrainCom);
        }

        return redirect()->back()->with('success', 'Le compte du client a été rechargé avec succès.');
    }

    private function createTransaction($senderUserId, $receiverUserId, $type, $amount)
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderUserId;
        $transaction->receiver_user_id = $receiverUserId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }

    public function indexBiicf()
    {
        $userId = Auth::guard('web')->id();
        Log::info('User ID:', ['user_id' => $userId]);

        $userWallet = Wallet::where('user_id', $userId)->first();
        Log::info('User Wallet:', ['wallet' => $userWallet]);

        // Récupérer les utilisateurs à exclure l'utilisateur authentifié
        $users = User::with('admin')
            ->where('id', '!=', $userId) // Exclure l'utilisateur authentifié
            ->orderBy('created_at', 'DESC')
            ->get();
        Log::info('Users (excluding authenticated user):', ['users' => $users]);

        $userCount = User::where('id', '!=', $userId)->count();
        Log::info('User Count (excluding authenticated user):', ['user_count' => $userCount]);

        // Récupérer les transactions impliquant l'utilisateur authentifié
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->orWhere('receiver_user_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
        Log::info('Transactions involving authenticated user:', ['transactions' => $transactions]);

        $transacCount = Transaction::where(function ($query) use ($userId) {
            $query->where('sender_user_id', $userId)
                ->orWhere('receiver_user_id', $userId);
        })->count();
        Log::info('Transaction Count involving authenticated user:', ['transaction_count' => $transacCount]);

        return view('biicf.wallet', compact('userWallet', 'users', 'userCount', 'transactions', 'transacCount', 'userId'));
    }

    public function retrait()
    {
        return view('biicf.retrait');
    }
}

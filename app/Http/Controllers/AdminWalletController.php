<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminWalletController extends Controller
{
    //
    public function index()
    {
        $adminId = Auth::guard('admin')->id();

        // Récupérer le portefeuille de l'administrateur connecté
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($adminId) {
                $query->where('sender_admin_id', $adminId)
                    ->orWhere('receiver_admin_id', $adminId);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $transacCount = $transactions->count();

        // Récupérer les 5 derniers agents
        $agents = Admin::where('admin_type', 'agent')
            ->orderBy('created_at', 'DESC')
            ->get();

        $agentCount = $agents->count();

        // Récupérer les 5 derniers utilisateurs
        $users = User::with('admin')
            ->orderBy('created_at', 'DESC')
            ->get();

        $userCount = $users->count();

        return view('admin.wallet', compact('adminWallet', 'transactions', 'transacCount', 'agents', 'users', 'agentCount', 'userCount', 'adminId'));
    }




    public function deposit(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'amount' => 'required|numeric|min:0',
        ]);

        // Récupérer l'ID de l'administrateur connecté
        $adminId = Auth::guard('admin')->id();

        // Créer une nouvelle transaction
        $transaction = new Transaction();
        $transaction->receiver_admin_id = $adminId;
        $transaction->type = 'Depot';
        $transaction->amount = $request->amount;
        $transaction->save();

        // Mettre à jour le solde du portefeuille de l'administrateur
        $adminWallet = Wallet::where('admin_id', $adminId)->first();
        $adminWallet->increment('balance', $request->amount);

        // Rediriger avec un message de succès
        return back()->with('success', 'Dépôt effectué avec succès.');
    }

    public function rechargeAgentAccount(Request $request)
    {
        $validatedData = $request->validate([
            'agent_id' => 'required',
            'amount' => 'required|numeric',
        ], [
            'agent_id.required' => 'Veuillez sélectionner un agent.',
            'amount.required' => 'Veuillez entrer le montant.',
            'amount.numeric' => 'Le montant doit être numérique.',
        ]);

        // Récupérer l'agent à partir de l'ID
        $agent = Admin::find($validatedData['agent_id']);

        // Vérifier si l'agent existe
        if (!$agent) {
            return redirect()->back()->with('error', 'L\'agent spécifié n\'existe pas.');
        }

        // Récupérer l'ID de l'administrateur actuel
        $adminId = Auth::guard('admin')->id();

        // Récupérer les portefeuilles de l'agent et de l'administrateur
        $agentWallet = Wallet::where('admin_id', $agent->id)->first();
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        // Vérifier si les portefeuilles existent
        if (!$agentWallet || !$adminWallet) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération des portefeuilles.');
        }

        // Vérifier si le solde de l'administrateur est suffisant pour la recharge
        if ($adminWallet->balance < $validatedData['amount']) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer la recharge.');
        }

        // Effectuer la recharge du compte de l'agent
        $agentWallet->increment('balance', $validatedData['amount']);
        $adminWallet->decrement('balance', $validatedData['amount']);

        $transaction1 = new Transaction();
        $transaction1->sender_admin_id = $adminId;
        $transaction1->receiver_admin_id = $agent->id;
        $transaction1->type = 'Reception';
        $transaction1->amount = $validatedData['amount'];
        $transaction1->save();

        $transaction2 = new Transaction();
        $transaction2->sender_admin_id = $adminId;
        $transaction2->receiver_admin_id = $agent->id;
        $transaction2->type = 'Envoie';
        $transaction2->amount = $validatedData['amount'];
        $transaction2->save();


        // Redirection avec un 1message de succès
        return redirect()->back()->with('success', 'Le compte de l\'agent a été rechargé avec succès.');
    }

    public function rechargeClientAccount(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required',
            'amount' => 'required|numeric',
        ]);

        $user = User::find($validatedData['user_id']);

        if (!$user) {
            return redirect()->back()->with('error', 'L\'utilisateur spécifié n\'existe pas.');
        }

        $adminId = Auth::guard('admin')->id();

        $userWallet = Wallet::where('user_id', $user->id)->first();
        $adminWallet = Wallet::where('admin_id', $adminId)->first();

        if (!$userWallet || !$adminWallet) {
            return redirect()->back()->with('error', 'Erreur lors de la récupération des portefeuilles.');
        }
        if ($adminWallet->balance < $validatedData['amount']) {
            return redirect()->back()->with('error', 'Solde insuffisant pour effectuer la recharge.');
        }
        $userWallet->increment('balance', $validatedData['amount']);
        $adminWallet->decrement('balance', $validatedData['amount']);

        $transaction1 = new Transaction();
        $transaction1->sender_admin_id = $adminId;
        $transaction1->receiver_user_id = $user->id;
        $transaction1->type = 'Reception';
        $transaction1->amount = $validatedData['amount'];
        $transaction1->save();

        $transaction2 = new Transaction();
        $transaction2->sender_admin_id = $adminId;
        $transaction2->receiver_user_id = $user->id;
        $transaction2->type = 'Envoie';
        $transaction2->amount = $validatedData['amount'];
        $transaction2->save();

        return redirect()->back()->with('success', 'Le compte de du client a été rechargé avec succès.');
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
    
        $userWallet = Wallet::where('user_id', $userId)->first();
    
        // Récupérer les utilisateurs à exclure l'utilisateur authentifié
        $users = User::with('admin')
            ->where('id', '!=', $userId) // Exclure l'utilisateur authentifié
            ->orderBy('created_at', 'DESC')
            ->get();
    
        $userCount = User::where('id', '!=', $userId)->count();
    
        // Récupérer les transactions impliquant l'utilisateur authentifié
        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($userId) {
                $query->where('sender_user_id', $userId)
                    ->orWhere('receiver_user_id', $userId);
            })
            ->orderBy('created_at', 'DESC')
            ->get();
    
        $transacCount = Transaction::where(function ($query) use ($userId) {
            $query->where('sender_user_id', $userId)
                ->orWhere('receiver_user_id', $userId);
        })->count();
    
        return view('biicf.wallet', compact('userWallet', 'users', 'userCount', 'transactions', 'transacCount', 'userId'));
    }
    
}

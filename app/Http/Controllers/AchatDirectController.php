<?php

namespace App\Http\Controllers;

use App\Models\AchatDirect;
use App\Models\NotificationEd;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\acceptAchat;
use App\Notifications\AchatBiicf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use App\Notifications\RefusAchat;


class AchatDirectController extends Controller
{
    public function store(Request $request)
    {
        // Valider les données du formulaire
        $validated = $request->validate([
            'nameProd' => 'required|string',
            'quantité' => 'required|integer',
            'montantTotal' => 'required|numeric',
            'localite' => 'required|string|max:255',
            'userTrader' => 'required|exists:users,id',
            'userSender' => 'required|exists:users,id',
            'specificite' => 'required|string',
            'photoProd' => 'required|string',
            'idProd' => 'required|exists:produit_services,id', // Correction ici, table correcte
        ]);

        // Récupérer l'utilisateur connecté
        $userId = Auth::id();

        // Vérifiez si l'utilisateur est authentifié
        if (!$userId) {
            return redirect()->back()->with('error', 'Utilisateur non authentifié.');
        }

        // Récupérer le portefeuille de l'utilisateur connecté
        $userWallet = Wallet::where('user_id', $userId)->first();

        // Vérifier si le portefeuille existe
        if (!$userWallet) {
            return redirect()->back()->with('error', 'Portefeuille introuvable.');
        }

        $requiredAmount = $validated['montantTotal'];

        // Vérifiez que le portefeuille de l'utilisateur a suffisamment de solde
        if ($userWallet->balance < $requiredAmount) {
            return redirect()->back()->with('error', 'Fonds insuffisants pour effectuer cet achat.');
        }

        try {
            // Créer une nouvelle achat
            $achat = AchatDirect::create([
                'nameProd' => $validated['nameProd'],
                'quantité' => $validated['quantité'],
                'montantTotal' => $validated['montantTotal'],
                'localite' => $validated['localite'],
                'userTrader' => $validated['userTrader'],
                'userSender' => $validated['userSender'],
                'specificite' => $validated['specificite'],
                'photoProd' => $validated['photoProd'],
                'idProd' => $validated['idProd'],
            ]);

            // Déduire le montant du solde de l'utilisateur
            $userWallet->decrement('balance', $requiredAmount);

            // Enregistrer la transaction pour l'utilisateur connecté
            $transaction = new Transaction();
            $transaction->sender_user_id = $userId;
            $transaction->receiver_user_id = $validated['userTrader'];
            $transaction->type = 'Gele';
            $transaction->amount = $validated['montantTotal'];
            $transaction->save();

            // Récupérer l'utilisateur propriétaire du produit
            $owner = User::find($validated['userTrader']);

            // Envoyer la notification au propriétaire du produit
            Notification::send($owner, new AchatBiicf($achat));



            return redirect()->back()->with('success', 'Achat passé avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function accepter(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $userId = Auth::guard('web')->id();



        // Récupérer le portefeuille de l'utilisateur connecté
        $userWallet = Wallet::where('user_id', $userId)->first();

        // Vérifier si le portefeuille existe
        if (!$userWallet) {
            return redirect()->back()->with('error', 'Portefeuille introuvable.');
        }

        // Valider les données de la requête
        $validatedData = $request->validate([
            'montantTotal' => 'required|numeric|min:1',
            'userSender' => 'required|integer|exists:users,id',
            'message' => 'required|string',
        ]);

        $notifId = $request->input('notifId');

        // Rechercher la notification par son identifiant
        $notification = NotificationEd::find($notifId);

        $notification->reponse = 'accepte';

        // Enregistrer les modifications
        $notification->save();

        // Récupérer les données validées
        $userSender = $validatedData['userSender'];
        $requiredAmount = $validatedData['montantTotal'];

        $pourcentSomme  = $requiredAmount * 0.1;

        $totalSom = $requiredAmount - $pourcentSomme;

        $userTrader = User::find($userId);
        $userSenders = User::find($userSender);



        if ($userTrader->parrain) {
            $commTraderParrain = $pourcentSomme * 0.05;

            $commTraderParrainWallet = Wallet::where('user_id', $userTrader->parrain)->first();

            $commTraderParrainWallet->increment('balance', $commTraderParrain);
        }

        if ($userSenders->parrain) {
            $commSenderParrain = $pourcentSomme * 0.05;

            $commSenderParrainWallet = Wallet::where('user_id', $userSenders->parrain)->first();

            $commSenderParrainWallet->increment('balance', $commSenderParrain);
        }

        // Incrémenter le solde du portefeuille de l'utilisateur connecté
        $userWallet->increment('balance', $totalSom);

        // Enregistrer la transaction de réception pour l'utilisateur connecté
        $this->createTransaction($userSender, $userId, 'Reception', $totalSom);

        // Enregistrer la transaction d'envoi pour l'utilisateur expéditeur
        $this->createTransaction($userSender, $userId, 'Envoie', $requiredAmount);

        // Enregistrer la transaction de commission pour l'utilisateur connecté

        if ($userTrader->parrain) {
            $this->createTransaction($userId, $userTrader->parrain, 'Commission', $commTraderParrain);
        }

        if ($userSenders->parrain) {
            $this->createTransaction($userSender, $userSenders->parrain, 'Commission', $commSenderParrain);
        }

        Notification::send($userSenders, new acceptAchat($validatedData['message']));


        return redirect()->back()->with('success', 'Achat accepté.');
    }

    /**
     * Créer et enregistrer une transaction.
     *
     * @param int $senderId
     * @param int $receiverId
     * @param string $type
     * @param float $amount
     * @return void
     */
    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }


    public function refuser(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Vérifier si l'utilisateur est authentifié
        if (!$userId) {
            return redirect()->back()->with('error', 'Utilisateur non authentifié.');
        }

        // Valider les données
        $validated = $request->validate([
            'montantTotal' => 'required|numeric|min:1',
            'userSender' => 'required|integer|exists:users,id',
            'message' => 'required|string',
        ]);

        $notifId = $request->input('notifId');

        // Rechercher la notification par son identifiant
        $notification = NotificationEd::find($notifId);

        $notification->reponse = 'refuser';

        // Enregistrer les modifications
        $notification->save();

        $userSender = $validated['userSender'];
        $requiredAmount = $validated['montantTotal'];

        // Récupérer le portefeuille de l'utilisateur de la notification
        $userWallet = Wallet::where('user_id', $userSender)->first();

        // Vérifier si le portefeuille existe
        if (!$userWallet) {
            return redirect()->back()->with('error', 'Portefeuille introuvable.');
        }


        // Décrémenter le solde du portefeuille
        $userWallet->increment('balance', $requiredAmount);

        // Enregistrer la transaction pour l'utilisateur connecté
        $transaction = new Transaction();
        $transaction->sender_user_id = $userId;
        $transaction->receiver_user_id = $userSender;
        $transaction->type = 'Reception';
        $transaction->amount = $requiredAmount;
        $transaction->save();

        // Récupérer l'utilisateur qui a envoyé l'achat
        $userSender = User::find($validated['userSender']);

        // Envoyer la notification au propriétaire du produit
        Notification::send($userSender, new refusAchat($validated['message']));

        return redirect()->back()->with('success', 'Achat refusé.');
    }
}

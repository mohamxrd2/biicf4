<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Countdown;

use App\Models\OffreGroupe;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\userquantites;
use App\Models\NotificationEd;
use App\Models\ProduitService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Notifications\OffreNegosNotif;
use Illuminate\Support\Facades\Notification;

class OffreNegos extends Controller
{
    //

    public function store(Request $request)
    {
        $user_id = Auth::guard('web')->id();

        // Récupérer l'ID du produit à partir du formulaire
        $produitId = $request->input('produit_id');

        $differance = $request->input('differance');

        $quantité = $request->input('quantite');

        // Trouver le produit ou échouer
        $produit = ProduitService::findOrFail($produitId);
        $nomProduit = $produit->name;

        $Uniquecode = $this->genererCodeAleatoire(10);

        $nomFournisseur = ProduitService::where('name', $nomProduit)
            ->where('user_id', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('user_id')
            ->toArray();
        
            userquantites::create([
                'quantite' =>  $quantité,
                'code_unique' =>  $Uniquecode,
                'user_id' =>  $user_id,
            ]);

        foreach ($nomFournisseur as $userId) {
            $user = User::find($userId);
            if ($user) {

                $data = [
                    'produit_id' => $produitId,
                    'produit_name' => $produit->name,
                    'quantite' => $quantité,
                    'code_unique' => $Uniquecode
                ];

                $owner = User::find($userId);

                //    Notification::send($user, new OffreNotifGroup($produit, $Uniquecode));

                Notification::send($owner, new OffreNegosNotif($data));
            }
        }
        // Insérer dans la table OffreGroupe
        OffreGroupe::create([
            'name' => $produit->name,
            'quantite' => $quantité,
            'code_unique' => $Uniquecode,
            'produit_id' => $produitId,
            'user_id' => $user_id,
            'differance' => $differance ?? null,
        ]);
        // Vérifier si un compte à rebours est déjà en cours pour cet code unique
        $existingCountdown = Countdown::where('code_unique', $Uniquecode)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Créer un nouveau compte à rebours s'il n'y en a pas en cours
            Countdown::create([
                'user_id' => Auth::id(),
                'start_time' => now(),
                'code_unique' => $Uniquecode,
                'difference' => 'offregroupe',
            ]);
        }

        return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
    }
    private function genererCodeAleatoire($longueur)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $code = '';

        for ($i = 0; $i < $longueur; $i++) {
            $code .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return $code;
    }

    public function add(Request $request)
    {
        // Récupérer l'identifiant de l'utilisateur connecté
        $user_id = Auth::guard('web')->id();

        // Récupérer les valeurs du formulaire
        $quantite = $request->input('quantite');
        $code_unique = $request->input('code_unique');
        $produit_name = $request->input('name');
        $produit_id = $request->input('produit_id'); // Assurez-vous que produit_id est inclus dans le formulaire

        $offreGroupeExistante = OffreGroupe::where('code_unique', $code_unique)->first();

        $differance = $offreGroupeExistante->differance;


        // Créer une nouvelle instance de OffreGroupe
        OffreGroupe::create([
            'name' => $produit_name,
            'quantite' => $quantite,
            'code_unique' => $code_unique,
            'produit_id' => $produit_id,
            'user_id' => $user_id,
            'differance' => $differance ?? null,
        ]);

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Quantité ajoutée avec succès !');
    }

    public function accepter(Request $request)
    {
        try {
            // Récupérer l'utilisateur connecté
            $userId = Auth::guard('web')->id();

            // Récupérer le portefeuille de l'utilisateur connecté
            $userWallet = Wallet::where('user_id', $userId)->first();
            if (!$userWallet) {
                return redirect()->back()->with('error', 'Portefeuille de l\'utilisateur introuvable.');
            }

            // Récupérer les données de la requête
            $requiredAmount = $request->input('prixarticle');
            $notifId = $request->input('notifId');

            $codeUnique = $request->input('code_unique');

            $distinctUserIds = OffreGroupe::where('code_unique', $codeUnique)
                ->distinct()
                ->pluck('user_id');

            // Rechercher la notification par son identifiant
            $notification = NotificationEd::find($notifId);
            if (!$notification) {
                return redirect()->back()->with('error', 'Notification introuvable.');
            }

            // Marquer la notification comme acceptée
            $notification->reponse = 'accepte';
            $notification->save();

            foreach ($distinctUserIds as $id_trader) {

                if (is_null($requiredAmount) || is_null($id_trader) || is_null($notifId)) {
                    return redirect()->back()->with('error', 'Données manquantes dans la requête.');
                }

                $traderWallet = Wallet::where('user_id', $id_trader)->first();
                if (!$traderWallet) {
                    return redirect()->back()->with('error', 'Portefeuille du trader introuvable.');
                }


                $traderWallet->increment('balance', $requiredAmount);


                $this->createTransaction($userId, $id_trader, 'Reception', $requiredAmount);
            }

            $userWallet->decrement('balance', $requiredAmount);

            $this->createTransaction($userId, $id_trader, 'Envoie', $requiredAmount);



            // Valider les données reçues




            // Récupérer le portefeuille du trader


            // Effectuer la transaction


            return redirect()->back()->with('success', 'Achat accepté.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'acceptation de l\'achat: ' . $e->getMessage());
        }
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

    public function offregroupneg()
    {
    }
}

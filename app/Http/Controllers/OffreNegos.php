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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OffreNegos extends Controller
{

    public function store(Request $request)
    {
        $user_id = Auth::guard('web')->id();

        Log::info('Attempting to store data', ['user_id' => $user_id]);

        // Validate request inputs
        $request->validate([
            'produit_id' => 'required|integer',
            'quantite' => 'required|numeric',
            'zone_economique' => 'required|string'
        ]);

        // Retrieve product ID and other inputs from the form
        $produitId = $request->input('produit_id');
        $differance = $request->input('differance');
        $quantité = $request->input('quantite');

        Log::info('Received request data', [
            'produit_id' => $produitId,
            'quantite' => $quantité,
            'zone_economique' => $request->input('zone_economique')
        ]);

        // Normalize economic zone input to lowercase
        $zone_economique = addslashes(strtolower($request->input('zone_economique')));


        // Find the product or fail
        $produit = ProduitService::findOrFail($produitId);
        $nomProduit = $produit->name;
        $referenceProduit = $produit->reference;

        Log::info('Product found', [
            'produit_id' => $produitId,
            'nomProduit' => $nomProduit,
            'reference' => $referenceProduit
        ]);

        // User's location details
        $userLocation = [
            'comnServ' => strtolower($produit->comnServ),
            'villeServ' => strtolower($produit->villeServ),
            'zonecoServ' => strtolower($produit->zonecoServ),
            'pays' => strtolower($produit->pays),
            'sous_region' => strtolower($produit->sous_region),
            'continent' => strtolower($produit->continent)
        ];

        // Map zone to produit location key
        $zoneMapping = [
            'proximite' => 'comnServ',
            'locale' => 'villeServ',
            'departementale' => 'zonecoServ',
            'nationale' => 'pays',
            'sous_regionale' => 'sous_region',
            'continentale' => 'continent'
        ];

        $appliedZoneKey = $zoneMapping[$zone_economique] ?? null;
        $appliedZoneValue = $userLocation[$appliedZoneKey] ?? null;

        if (!$appliedZoneValue) {
            Log::warning('Invalid economic zone selected', ['zone_economique' => $zone_economique]);
            return redirect()->back()->with('error', 'Zone économique non valide.');
        }

        // Generate a unique code
        $Uniquecode = $this->genererCodeAleatoire(10);

        Log::info('Generated unique code', ['Uniquecode' => $Uniquecode]);

        // Find suppliers with the same product reference within the selected economic zone
        $nomFournisseur = ProduitService::where('reference', $referenceProduit)
            ->where('user_id', '!=', $user_id) // Exclude the current user
            ->where('statuts', 'Accepté')
            ->whereHas('user', function ($query) use ($appliedZoneKey, $appliedZoneValue) {
                $query->where($appliedZoneKey, $appliedZoneValue);
            })
            ->pluck('user_id')
            ->toArray();

        if (empty($nomFournisseur)) {
            Log::warning('No suppliers found in the selected economic zone', [
                'reference' => $referenceProduit,
                'zone' => $appliedZoneKey,
                'value' => $appliedZoneValue
            ]);
            return redirect()->back()->with('error', 'Aucun fournisseur trouvé dans la zone économique sélectionnée.');
        }

        Log::info('Found suppliers', ['suppliers' => $nomFournisseur]);

        // Store the quantity and unique code in userquantites table
        userquantites::create([
            'quantite' => $quantité,
            'code_unique' => $Uniquecode,
            'user_id' => $user_id,
        ]);

        Log::info('Stored user quantity', [
            'quantite' => $quantité,
            'code_unique' => $Uniquecode,
            'user_id' => $user_id,
        ]);

        // Send notifications to relevant suppliers
        foreach ($nomFournisseur as $supplierId) {
            $supplier = User::find($supplierId);
            if ($supplier) {
                $data = [
                    'produit_id' => $produitId,
                    'produit_name' => $produit->name,
                    'quantite' => $quantité,
                    'code_unique' => $Uniquecode
                ];

                // Sending the notification
                Notification::send($supplier, new OffreNegosNotif($data));
                Log::info('Notification sent', [
                    'supplier_id' => $supplierId,
                    'data' => $data
                ]);
            }
        }

        // Insert into the OffreGroupe table
        OffreGroupe::create([
            'name' => $produit->name,
            'quantite' => $quantité,
            'code_unique' => $Uniquecode,
            'produit_id' => $produitId,
            'zone' => $zone_economique,
            'user_id' => $user_id,
            'differance' => $differance ?? null,
        ]);

        Log::info('Inserted into OffreGroupe', [
            'name' => $produit->name,
            'quantite' => $quantité,
            'code_unique' => $Uniquecode,
            'zone' => $zone_economique,
            'produit_id' => $produitId,
            'user_id' => $user_id,
        ]);

        // Check if a countdown is already running for this unique code
        $existingCountdown = Countdown::where('code_unique', $Uniquecode)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            // Create a new countdown if none exists
            Countdown::create([
                'user_id' => Auth::id(),
                'start_time' => now(),
                'code_unique' => $Uniquecode,
                'difference' => 'offregroupe',
            ]);

            Log::info('Countdown started', [
                'user_id' => Auth::id(),
                'start_time' => now(),
                'code_unique' => $Uniquecode,
            ]);
        } else {
            Log::info('Countdown already exists for the unique code', ['code_unique' => $Uniquecode]);
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

    public function offregroupneg() {}
}

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
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use InvalidArgumentException;

class OffreNegos extends Controller
{

    public function store(Request $request)
    {
        try {
            $user_id = Auth::id();
            Log::info('Tentative de stockage de données', ['user_id' => $user_id]);

            // Validation des entrées
            $validatedData = $request->validate([
                'produit_id' => 'required|integer',
                'quantite' => 'required|numeric',
                'username' => 'required|string|max:255',
                'zone_economique' => 'required|string',
            ]);

            // Recherche du produit et de l'utilisateur
            $produit = ProduitService::findOrFail($validatedData['produit_id']);
            $username = User::where('username', $validatedData['username'])->firstOrFail();
            $zoneKey = $this->mapEconomicZone($validatedData['zone_economique'], $produit->user);


            // Générer un code unique
            $uniqueCode = $this->generateUniqueReference();

            // Trouver les fournisseurs pertinents
            $suppliers = $this->findSuppliers($produit, $user_id, $zoneKey);

            if (empty($suppliers)) {
                return $this->handleNoSuppliers($produit, $zoneKey);
            }

            // Notifier les fournisseurs
            $this->notifySuppliers($suppliers, $produit, $validatedData['quantite'], $uniqueCode);

            // Insérer dans `OffreGroupe`
            $this->saveOffreGroupe($validatedData, $produit, $user_id, $uniqueCode);

            // Gestion du compte à rebours
            $this->handleCountdown($uniqueCode, $username);

            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (Exception $e) {
            Log::error('Erreur lors du stockage des données', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Une erreur est survenue. Veuillez réessayer.');
        }
    }


    private function mapEconomicZone($zoneEconomique, $user)
    {
        $zoneMapping = [
            'proximite' => 'commune',
            'locale' => 'ville',
            'departementale' => 'departe',
            'nationale' => 'pays',
            'sous_regionale' => 'sous_region',
            'continentale' => 'continent',
        ];

        $zoneKey = $zoneMapping[strtolower($zoneEconomique)] ?? null;
        if (!$zoneKey || !isset($user->$zoneKey)) {
            throw new InvalidArgumentException('Zone économique invalide.');
        }

        Log::info('Zone économique mappée', ['zone_key' => $zoneKey, 'value' => $user->$zoneKey]);
        return $zoneKey;
    }
    private function findSuppliers($produit, $userId, $zoneKey)
    {
        $suppliers = ProduitService::where('reference', $produit->reference)
            ->where('user_id', '!=', $userId)
            ->where('statuts', 'Accepté')
            ->whereHas('user', fn($query) => $query->where($zoneKey, $produit->user->$zoneKey))
            ->pluck('user_id')
            ->toArray();

        Log::info('Fournisseurs trouvés', ['suppliers' => $suppliers]);
        return $suppliers;
    }

    private function notifySuppliers(array $suppliers, $produit, $quantite, $uniqueCode)
    {
        foreach ($suppliers as $supplierId) {
            $supplier = User::find($supplierId);
            if ($supplier) {
                Notification::send($supplier, new OffreNegosNotif([
                    'idProd' => $produit->id,
                    'produit_name' => $produit->name,
                    'quantite' => $quantite,
                    'code_unique' => $uniqueCode,
                ]));

                Log::info('Notification envoyée', ['supplier_id' => $supplierId]);
            }
        }
    }
    private function saveOffreGroupe($data, $produit, $userId, $uniqueCode)
    {
        OffreGroupe::create([
            'name' => $produit->name,
            'quantite' => $data['quantite'],
            'code_unique' => $uniqueCode,
            'produit_id' => $data['produit_id'],
            'zone' => $data['zone_economique'],
            'user_id' => $userId,
            'differance' => 'grouper',
        ]);

        Log::info('OffreGroupe enregistrée', [
            'name' => $produit->name,
            'code_unique' => $uniqueCode,
        ]);
    }
    private function handleCountdown($uniqueCode, $username)
    {
        $existingCountdown = Countdown::where('code_unique', $uniqueCode)
            ->where('notified', false)
            ->orderBy('start_time', 'desc')
            ->first();

        if (!$existingCountdown) {
            Countdown::create([
                'user_id' => Auth::id(),
                'userSender' => $username->id,
                'start_time' => now(),
                'code_unique' => $uniqueCode,
                'difference' => 'offregroupe',
            ]);

            Log::info('Compte à rebours créé', ['code_unique' => $uniqueCode]);
        } else {
            Log::info('Compte à rebours déjà existant', ['code_unique' => $uniqueCode]);
        }
    }


    // Fonction pour générer un code de référence de 5 chiffres

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
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

    
    protected function createTransaction(int $senderId, int $receiverId, string $type, float $amount): void
    {
        $transaction = new Transaction();
        $transaction->sender_user_id = $senderId;
        $transaction->receiver_user_id = $receiverId;
        $transaction->type = $type;
        $transaction->amount = $amount;
        $transaction->save();
    }
}

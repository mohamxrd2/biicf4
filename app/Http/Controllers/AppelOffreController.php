<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use App\Models\NotificationLog;
use App\Models\AppelOffreGrouper;
use App\Models\Consommation;
use App\Models\Countdown;
use App\Models\Transaction;
use App\Models\userquantites;
use App\Models\Wallet;
use App\Notifications\AOGrouper;
use App\Notifications\AppelOffre;
use App\Notifications\AppelOffreGrouper as NotificationsAppelOffreGrouper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class AppelOffreController extends Controller
{
    public function search(Request $request)
    {
        $userId = Auth::guard('web')->id();
        $keyword = $request->input('keyword');
        $type = $request->input('type');

        // Normalize keyword for case-insensitivity and apostrophe handling
        $normalizedKeyword = strtolower(str_replace("'", "''", $keyword));
        $normalizedType = strtolower($type);

        // Initial query
        $produitsQuery = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->where('user_id', '<>', $userId)
            ->orderBy('created_at', 'desc');

        Log::info('Initial query built', [
            'keyword' => $keyword,
            'type' => $type,
        ]);

        // Apply keyword filter
        if ($keyword) {
            $produitsQuery->whereRaw('LOWER(name) LIKE ?', ['%' . $normalizedKeyword . '%']);
            Log::info('Applied keyword filter', ['keyword' => $normalizedKeyword]);
        }

        // Apply type filter
        if ($type) {
            $produitsQuery->whereRaw('LOWER(type) = ?', [$normalizedType]);
            Log::info('Applied type filter', ['type' => $normalizedType]);
        }

        // Fetch the products with initial filters
        $results = $produitsQuery->get();
        Log::info('Results fetched', ['results_count' => $results->count()]);

        // Log references before grouping
        Log::info('References before grouping', ['references' => $results->pluck('reference')->unique()]);

        // Group results by reference
        $groupedByReference = $results->groupBy('reference');

        // Filter based on zone économique

        $filtered = collect();

        $zoneEconomique = $request->input('zone_economique');
        $normalizedZoneEconomique = strtolower($zoneEconomique);
        $user = User::find($userId);
        $userZone = $user ? strtolower($user->commune) : null;
        $userVille = $user ? strtolower($user->ville) : null;
        $userDepartement = $user ? strtolower($user->departe) : null;
        $userPays = $user ? strtolower($user->country) : null;
        $userSousRegion = $user ? strtolower($user->sous_region) : null;
        $userContinent = $user ? strtolower($user->continent) : null;

        $appliedZoneValue = null; // Variable pour stocker la valeur choisie

        if ($zoneEconomique) {
            switch ($normalizedZoneEconomique) {
                case 'proximite':
                    if ($userZone) {
                        $filtered = $groupedByReference->map(function ($group) use ($userZone) {
                            return $group->filter(function ($produit) use ($userZone) {
                                return strtolower($produit->comnServ) === $userZone;
                            });
                        });
                        $appliedZoneValue = $userZone;
                        Log::info('Filtre Proximité appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userZone' => $userZone]);
                    }
                    break;

                case 'locale':
                    if ($userVille) {
                        $filtered = $groupedByReference->map(function ($group) use ($userVille) {
                            return $group->filter(function ($produit) use ($userVille) {
                                return strtolower($produit->villeServ) === $userVille;
                            });
                        });
                        $appliedZoneValue = $userVille;
                        Log::info('Filtre Locale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userVille' => $userVille]);
                    }
                    break;

                case 'departementale':
                    if ($userDepartement) {
                        $filtered = $groupedByReference->map(function ($group) use ($userDepartement) {
                            return $group->filter(function ($produit) use ($userDepartement) {
                                return strtolower($produit->zonecoServ) === $userDepartement;
                            });
                        });
                        $appliedZoneValue = $userDepartement;
                        Log::info('Filtre Département appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userDepartement' => $userDepartement]);
                    }
                    break;

                case 'nationale':
                    if ($userPays) {
                        $filtered = $groupedByReference->map(function ($group) use ($userPays) {
                            return $group->filter(function ($produit) use ($userPays) {
                                return strtolower($produit->pays) === $userPays;
                            });
                        });
                        $appliedZoneValue = $userPays;
                        Log::info('Filtre Nationale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userPays' => $userPays]);
                    }
                    break;

                case 'sous_regionale':
                    if ($userSousRegion) {
                        $filtered = $groupedByReference->map(function ($group) use ($userSousRegion) {
                            return $group->filter(function ($produit) use ($userSousRegion) {
                                return strtolower($produit->sous_region) === $userSousRegion;
                            });
                        });
                        $appliedZoneValue = $userSousRegion;
                        Log::info('Filtre Sous-régionale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userSousRegion' => $userSousRegion]);
                    }
                    break;

                case 'continentale':
                    if ($userContinent) {
                        $filtered = $groupedByReference->map(function ($group) use ($userContinent) {
                            return $group->filter(function ($produit) use ($userContinent) {
                                return strtolower($produit->continent) === $userContinent;
                            });
                        });
                        $appliedZoneValue = $userContinent;
                        Log::info('Filtre Continentale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userContinent' => $userContinent]);
                    }
                    break;

                default:
                    Log::info('Zone économique non reconnue', ['zoneEconomique' => $normalizedZoneEconomique]);
                    break;
            }
        }

        // Vous pouvez maintenant utiliser la variable $appliedZoneValue pour connaître la valeur finale de la zone économique appliquée



        // Remove empty groups
        $filtered = $filtered->filter(function ($group) {
            return $group->isNotEmpty();
        });

        // Log grouped user_ids by reference
        foreach ($filtered as $reference => $group) {
            $user_ids = $group->pluck('user_id')->unique();
            Log::info('User IDs grouped by reference', [
                'reference' => $reference,
                'user_ids' => $user_ids->toArray()
            ]);
        }


        // Proceed with using $filtered for further operations


        $resultCount = $results->count();

        $prodUsers = $results->pluck('user.id')->unique()->toArray();
        $lowestPricedProduct = $results->min('prix');
        $prodUsersCount = $results->pluck('user')->unique('id')->count();

        $produitDims = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->where('user_id', '<>', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        



        return view('biicf.searchAppelOffre', compact('filtered', 'groupedByReference', 'results', 'appliedZoneValue', 'resultCount', 'zoneEconomique', 'keyword', 'prodUsers', 'produitDims', 'prodUsersCount', 'lowestPricedProduct'));
    }

    public function formAppel(Request $request)
    {

        $name = $request->input('name');
        $lowestPricedProduct = $request->input('lowestPricedProduct');
        $prodUsers = $request->input('prodUsers');
        $reference = $request->input('reference');
        $distinctSpecifications = $request->input('distinctSpecifications');
        $appliedZoneValue = $request->input('appliedZoneValue');
        $type = $request->input('type');

        // Convert inputs to arrays if they are not already
        $prodUsers = is_array($prodUsers) ? $prodUsers : (is_object($prodUsers) ? $prodUsers->toArray() : []);
        $distinctSpecifications = is_array($distinctSpecifications) ? $distinctSpecifications : (is_string($distinctSpecifications) ? explode(',', $distinctSpecifications) : []);



        return view('biicf.formappel', compact('lowestPricedProduct', 'type', 'prodUsers', 'name', 'reference', 'distinctSpecifications', 'appliedZoneValue'));
    }

    public function detailoffre(Request $request, $id)
    {
        // Récupérer l'ID de l'utilisateur connecté
        $userId = Auth::guard('web')->id();

        // Récupérer l'offre groupée par son ID
        $appelOffreGroup = AppelOffreGrouper::find($id);

        // Vérifier si l'offre groupée existe
        if (!$appelOffreGroup) {
            return redirect()->route('biicf.appeloffre');
        }

        // Récupérer les variables
        $codesUniques = $appelOffreGroup->codeunique;
        $dateTot = $appelOffreGroup->dateTot;
        $dateTard = $appelOffreGroup->dateTard;
        $productName = $appelOffreGroup->productName;
        $quantity = $appelOffreGroup->quantity;
        $payment = $appelOffreGroup->payment;
        $livraison = $appelOffreGroup->livraison;
        $specificity = $appelOffreGroup->specificity;
        $lowestPricedProduct = $appelOffreGroup->lowestPricedProduct;

        // // Récupérer les utilisateurs associés à l'offre groupée
        // $usergroup = AppelOffreGrouper::where('codeunique', $codesUniques)
        //     ->distinct()
        //     ->pluck('user_id')
        //     ->toArray();

        // $prodUsers = AppelOffreGrouper::where('codeunique', $codesUniques)
        //     ->distinct()
        //     ->pluck('prodUsers')
        //     ->toArray();

        // // Décoder les valeurs JSON de $prodUsers en entiers
        // $decodedProdUsers = [];
        // foreach ($prodUsers as $prodUser) {
        //     $decodedValues = json_decode($prodUser, true);
        //     if (is_array($decodedValues)) {
        //         $decodedProdUsers = array_merge($decodedProdUsers, $decodedValues);
        //     }
        // }

        // // Récupérer la date la plus ancienne pour le code unique
        // $datePlusAncienne = AppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');

        // // Ajouter 1 minute à la date la plus ancienne, s'il y en a une
        // $tempsEcoule = $datePlusAncienne ? Carbon::parse($datePlusAncienne)->addMinutes(1) : null;

        // // Vérifier si $tempsEcoule est écoulé
        // $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();

        // $sumquantite = AppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');

        // // Compter le nombre distinct d'utilisateurs pour le code unique
        // $appelOffreGroupcount = AppelOffreGrouper::where('codeunique', $codesUniques)
        //     ->distinct('user_id')
        //     ->count('user_id');

        // $notificationExists = NotificationLog::where('code_unique', $codesUniques)->exists();

        // if ($isTempsEcoule && !$notificationExists) {
        //     foreach ($decodedProdUsers as $prodUser) {
        //         $data = [
        //             'dateTot' => $dateTot,
        //             'dateTard' => $dateTard,
        //             'productName' => $productName,
        //             'quantity' => $sumquantite,
        //             'payment' => $payment,
        //             'Livraison' => $livraison,
        //             'specificity' => $specificity,
        //             'difference' => 'grouper',
        //             'image' => null, // Gérer l'upload et le stockage de l'image si nécessaire
        //             'id_sender' => $usergroup,
        //             'prodUsers' => $prodUser,
        //             'lowestPricedProduct' => $lowestPricedProduct,
        //             'code_unique' => $codesUniques,
        //         ];

        //         // Vérification que toutes les clés nécessaires sont présentes
        //         $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'image', 'prodUsers', 'code_unique'];
        //         foreach ($requiredKeys as $key) {
        //             if (!array_key_exists($key, $data)) {
        //                 throw new \InvalidArgumentException("La clé '$key' est manquante dans \$data.");
        //             }
        //         }

        //         // Récupération de l'utilisateur destinataire
        //         $owner = User::find($prodUser);

        //         // Vérification si l'utilisateur existe
        //         if ($owner) {
        //             // Envoi de la notification à l'utilisateur
        //             // Notification::send($owner, new AppelOffre($data));
        //         }
        //     }
        // }

        // Passer les variables à la vue (si nécessaire)
        return view('biicf.ajoutoffre', compact('userId', 'appelOffreGroup', 'datePlusAncienne', 'tempsEcoule', 'sumquantite', 'appelOffreGroupcount'));
    }

    public function storeoffre(Request $request)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'codeUnique' => 'required|string',
            'userId' => 'required|integer',
            'quantite' => 'required|integer'
        ]);

        // Créer un nouvel enregistrement dans la table offregroupe
        $offregroupe = new AppelOffreGrouper();
        $offregroupe->codeunique = $validatedData['codeUnique'];
        $offregroupe->user_id = $validatedData['userId'];
        $offregroupe->quantity = $validatedData['quantite'];
        $offregroupe->save();

        //ajout dans table userquantites
        $quantite = new userquantites();
        $quantite->code_unique = $validatedData['codeUnique'];
        $quantite->user_id = $validatedData['userId'];
        $quantite->quantite = $validatedData['quantite'];
        $quantite->save();

        return redirect()->back()->with('success', 'Quantité ajouter avec succès');
    }

    public function storeAppel(Request $request)
    {
        try {
            $userId = Auth::guard('web')->id();

            // Validation des données du formulaire
            $request->validate([
                'productName' => 'required|string',
                'quantity' => 'required|integer',
                'payment' => 'required|string',
                'Livraison' => 'required|string', // Ensure consistent naming
                'dateTot' => 'required|date',
                'dateTard' => 'required|date',
                'specification' => 'nullable|string',
                'reference' => 'required|string',
                'localite' => 'nullable|string',
                'image' => 'nullable',
                'prodUsers' => 'required|array',
            ]);

            $lowestPricedProduct = $request->input('lowestPricedProduct');
            $prodUsers = $request->input('prodUsers');

            // Vérification que $prodUsers est un tableau non vide
            if (empty($prodUsers)) {
                return redirect()->back()->with('error', 'Aucun utilisateur de produit spécifié.');
            }



            // Générer un code unique une seule fois pour tous les utilisateurs
            $codeUnique = $this->genererCodeAleatoire(10);

            // Vérifiez si l'utilisateur a un portefeuille
            $userId = Auth::id();
            $userWallet = Wallet::where('user_id', $userId)->first();
            // Calculate the total cost (replace 'price' with the actual price logic)
            $totalCost = $request->quantity * $lowestPricedProduct;

            // Vérification du solde du portefeuille
            if ($userWallet->balance < $totalCost) {
                return redirect()->back()->with('error', 'Solde insuffisant dans le portefeuille pour effectuer cette transaction.');
            }

            // Augmentation du solde du portefeuille du client
            $userWallet->decrement('balance', $totalCost);

            // Création de la transaction
            $this->createTransaction($userId, $userId, 'Gele', $totalCost);

            // Boucle sur chaque utilisateur pour envoyer la notification
            foreach ($prodUsers as $prodUser) {
                $data = [
                    'dateTot' => $request->dateTot,
                    'dateTard' => $request->dateTard,
                    'productName' => $request->productName,
                    'quantity' => $request->quantity,
                    'payment' => $request->payment,
                    'Livraison' => $request->Livraison, // Ensure consistent naming
                    'specificity' => $request->specification,
                    'reference' => $request->reference,
                    'localite' => $request->localite,
                    'image' => null, // Gérer l'upload et le stockage de l'image si nécessaire
                    'id_sender' => $userId,
                    'prodUsers' => $prodUser,
                    'lowestPricedProduct' => $lowestPricedProduct,
                    'code_unique' => $codeUnique,
                    'difference' => 'single',
                ];

                // Vérification que toutes les clés nécessaires sont présentes
                $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'reference', 'image', 'prodUsers', 'lowestPricedProduct', 'difference'];
                foreach ($requiredKeys as $key) {
                    if (!array_key_exists($key, $data)) {
                        throw new \InvalidArgumentException("La clé '$key' est manquante dans \$data.");
                    }
                }

                // Récupération de l'utilisateur destinataire
                $owner = User::find($prodUser);

                // Vérification si l'utilisateur existe
                if ($owner) {
                    // Envoi de la notification à l'utilisateur
                    Notification::send($owner, new AppelOffre($data));

                    // Récupérez la notification pour mise à jour (en supposant que vous pouvez la retrouver via son ID ou une autre méthode)
                    $notification = $owner->notifications()->where('type', AppelOffre::class)->latest()->first();

                    if ($notification) {
                        // Mettez à jour le champ 'type_achat' dans la notification
                        $notification->update(['type_achat' => $request->Livraison]);
                    }
                }
            }

            return redirect()->route('biicf.appeloffre')->with('success', 'Notification envoyée avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('biicf.appeloffre')->with('error', 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
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

    public function formstoreGroupe(Request $request)
    {
        try {
            Log::info('Début du traitement dans formstoreGroupe');

            // Récupérer l'ID de l'utilisateur connecté
            $userId = Auth::guard('web')->id();
            Log::info('Utilisateur connecté ID:', ['user_id' => $userId]);

            // Générer un code unique une seule fois pour tous les utilisateurs
            $codeunique = $this->genererCodeAleatoire(10);
            Log::info('Code unique généré:', ['codeunique' => $codeunique]);

            // Valider les données de la requête
            $validatedData = $request->validate([
                'productName' => 'required|string',
                'quantity' => 'required|integer',
                'payment' => 'required|string',
                'Livraison' => 'required|string',
                'dateTot' => 'required|date',
                'dateTard' => 'required|date',
                'specification' => 'required|string',
                'localite' => 'required|string',
                'appliedZoneValue' => 'required|string',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'prodUsers' => 'required|array',
            ]);
            Log::info('Données de la requête validées.');

            // Gérer le téléchargement du fichier
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
                Log::info('Image téléchargée:', ['image_path' => $imagePath]);
            }

            // Créer une nouvelle instance du modèle
            $offre = new AppelOffreGrouper();
            $offre->productName = $validatedData['productName'];
            $offre->lowestPricedProduct = $request->input('lowestPricedProduct', null); // Utiliser null comme valeur par défaut si non présent
            $offre->quantity = $validatedData['quantity'];
            $offre->payment = $validatedData['payment'];
            $offre->Livraison = $validatedData['Livraison'];
            $offre->dateTot = $validatedData['dateTot'];
            $offre->dateTard = $validatedData['dateTard'];
            $offre->specificity = $validatedData['specification'];
            $offre->localite = $validatedData['localite'];
            $offre->prodUsers = json_encode($validatedData['prodUsers']);
            $offre->codeunique = $codeunique;
            $offre->user_id = $userId;

            // Ajouter le chemin de l'image s'il existe
            if ($imagePath) {
                $offre->image = $imagePath;
            }

            // Sauvegarder le modèle
            $offre->save();
            Log::info('Offre enregistrée avec succès.', ['offre' => $offre]);

            // Créer une nouvelle instance du modèle userquantites
            $quantite = new userquantites();
            $quantite->user_id = $userId;
            $quantite->quantite = $validatedData['quantity'];
            $quantite->code_unique = $codeunique;
            $quantite->save();
            Log::info('Quantité enregistrée avec succès.', ['quantite_id' => $quantite->id]);

            // Vérifiez si l'utilisateur a un portefeuille
            $userId = Auth::id();
            $userWallet = Wallet::where('user_id', $userId)->first();
            // Calculate the total cost (replace 'price' with the actual price logic)
            $totalCost = $validatedData['quantity'] * $request->input('lowestPricedProduct', null);

            // Vérification du solde du portefeuille
            if ($userWallet->balance < $totalCost) {
                return redirect()->back()->with('error', 'Solde insuffisant dans le portefeuille pour effectuer cette transaction.');
            }

            // Augmentation du solde du portefeuille du client
            $userWallet->decrement('balance', $totalCost);

            // Création de la transaction
            $this->createTransaction($userId, $userId, 'Gele', $totalCost);

            // Récupérer les IDs des propriétaires des consommations similaires
            $idsProprietaires = Consommation::where('name', $offre->productName)
                ->where('id_user', '!=', $userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();
            Log::info('IDs des propriétaires récupérés:', ['ids_proprietaires' => $idsProprietaires]);

            // Vérifier que nous avons des propriétaires pour éviter des erreurs de requêtes
            if (!empty($idsProprietaires)) {
                // Récupérer les IDs des utilisateurs avec la même localité et qui possèdent le produit
                $idsLocalite = User::whereIn('id', $idsProprietaires) // Sélectionner uniquement parmi les propriétaires
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
                Log::info('IDs des utilisateurs avec la même localité récupérés:', ['ids_localite' => $idsLocalite]);

                // Fusionner les deux tableaux d'IDs
                $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            } else {
                // Si aucun propriétaire n'est trouvé, seulement les utilisateurs avec la même localité sont considérés
                return redirect()->back()->with('error', 'Aucun utilisateur ne consomme ce produit dans votre zone économique. Veuillez changer de zone ou de fonctionnalité.');
            }

            // Envoyer une notification à chaque utilisateur
            foreach ($idsToNotify as $id) {
                $user = User::find($id);
                if ($user) {
                    try {
                        // Envoyer une notification avec le code unique
                        Notification::send($user, new AOGrouper($codeunique, $offre->id));
                        Log::info('Notification envoyée à l\'utilisateur:', ['user_id' => $id]);
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'envoi de la notification:', ['user_id' => $id, 'exception' => $e->getMessage()]);
                    }
                }
            }


            Log::info('Traitement terminé avec succès');

            return redirect()->route('biicf.appeloffre')->with('success', 'Notification envoyée avec succès!');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi de la notification:', ['error' => $e->getMessage()]);
            return redirect()->route('biicf.appeloffre')->with('error', 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
        }
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
}

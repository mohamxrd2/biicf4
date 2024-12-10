<?php

namespace App\Http\Controllers;

use App\Models\AppelOffreUser;
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
        // Log::info('References before grouping', ['references' => $results->pluck('reference')->unique()]);

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
                                return strtolower($produit->user->commune) === $userZone;
                            });
                        });
                        $appliedZoneValue = $userZone;
                        // Log::info('Filtre Proximité appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userZone' => $userZone]);
                    }
                    break;

                case 'locale':
                    if ($userVille) {
                        $filtered = $groupedByReference->map(function ($group) use ($userVille) {
                            return $group->filter(function ($produit) use ($userVille) {
                                return strtolower($produit->user->ville) === $userVille;
                            });
                        });
                        $appliedZoneValue = $userVille;
                        // Log::info('Filtre Locale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userVille' => $userVille]);
                    }
                    break;

                case 'departementale':
                    if ($userDepartement) {
                        $filtered = $groupedByReference->map(function ($group) use ($userDepartement) {
                            return $group->filter(function ($produit) use ($userDepartement) {
                                return strtolower($produit->user->departe) === $userDepartement;
                            });
                        });
                        $appliedZoneValue = $userDepartement;
                        // Log::info('Filtre Département appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userDepartement' => $userDepartement]);
                    }
                    break;

                case 'nationale':
                    if ($userPays) {
                        $filtered = $groupedByReference->map(function ($group) use ($userPays) {
                            return $group->filter(function ($produit) use ($userPays) {
                                return strtolower($produit->user->country) === $userPays;
                            });
                        });
                        $appliedZoneValue = $userPays;
                        // Log::info('Filtre Nationale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userPays' => $userPays]);
                    }
                    break;

                case 'sous_regionale':
                    if ($userSousRegion) {
                        $filtered = $groupedByReference->map(function ($group) use ($userSousRegion) {
                            return $group->filter(function ($produit) use ($userSousRegion) {
                                return strtolower($produit->user->sous_region) === $userSousRegion;
                            });
                        });
                        $appliedZoneValue = $userSousRegion;
                        // Log::info('Filtre Sous-régionale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userSousRegion' => $userSousRegion]);
                    }
                    break;

                case 'continentale':
                    if ($userContinent) {
                        $filtered = $groupedByReference->map(function ($group) use ($userContinent) {
                            return $group->filter(function ($produit) use ($userContinent) {
                                return strtolower($produit->user->continent) === $userContinent;
                            });
                        });
                        $appliedZoneValue = $userContinent;
                        // Log::info('Filtre Continentale appliqué', ['zoneEconomique' => $normalizedZoneEconomique, 'userContinent' => $userContinent]);
                    }
                    break;

                default:
                    // Log::info('Zone économique non reconnue', ['zoneEconomique' => $normalizedZoneEconomique]);
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
            // Log::info('User IDs grouped by reference', [
            //     'reference' => $reference,
            //     'user_ids' => $user_ids->toArray()
            // ]);
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
        $distinctquatiteMin = $request->input('distinctquatiteMin');
        $distinctquatiteMax = $request->input('distinctquatiteMax');
        $prodUsers = $request->input('prodUsers');
        $reference = $request->input('reference');
        $distinctSpecifications = $request->input('distinctSpecifications');
        $distinctCondProds = $request->input('distinctCondProds');
        $appliedZoneValue = $request->input('appliedZoneValue');
        $type = $request->input('type');

        // Convert inputs to arrays if they are not already
        $prodUsers = (array) $prodUsers;
        $distinctSpecifications = is_array($distinctSpecifications) ? $distinctSpecifications : explode(',', $distinctSpecifications);

        $userId = Auth::id();
        // Récupérer la balance pour un utilisateur donné
        $wallet = Wallet::where('user_id', $userId)->first();
        if (!$wallet) {
            // Gérer le cas où l'utilisateur n'a pas de portefeuille
            $wallet = new Wallet(['user_id' => $userId, 'balance' => 0]); // Exemple de création d'un portefeuille par défaut
        }


        return view('biicf.formappel', [
            'wallet' => $wallet,
            'lowestPricedProduct' => $lowestPricedProduct,
            'distinctCondProds' => $distinctCondProds,
            'type' => $type,
            'prodUsers' => $prodUsers,
            'distinctquatiteMax' => $distinctquatiteMax,
            'distinctquatiteMin' => $distinctquatiteMin,
            'name' => $name,
            'reference' => $reference,
            'distinctSpecifications' => $distinctSpecifications,
            'appliedZoneValue' => $appliedZoneValue,
        ]);
    }
}

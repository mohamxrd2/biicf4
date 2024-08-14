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
use App\Models\userquantites;
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
        $zoneEconomique = $request->input('zone_economique');
        $type = $request->input('type');

        // Faire la recherche dans la base de données en fonction des filtres
        $produits = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc');

        Log::info('Initial query built', [
            'keyword' => $keyword,
            'zoneEconomique' => $zoneEconomique,
            'type' => $type,
        ]);

        if ($keyword) {
            $produits->where('name', 'like', '%' . $keyword . '%');
            Log::info('Applied keyword filter', ['keyword' => $keyword]);
        }

        if ($zoneEconomique) {
            $produits->where('zonecoServ', $zoneEconomique);
            Log::info('Applied zoneEconomique filter', ['zoneEconomique' => $zoneEconomique]);
        }

        if ($type) {
            $produits->where('type', $type);
            Log::info('Applied type filter', ['type' => $type]);
        }
        //////////
        $results = $produits->where('user_id', '<>', $userId)->get();
        Log::info('Results fetched', ['results_count' => $results->count()]);

        Log::info('References before grouping', ['references' => $results->pluck('reference')->unique()]);

        // Grouper les résultats par code de référence
        $groupedByReference = $results->groupBy('reference');

        // Parcourir les groupes pour afficher les informations demandées
        foreach ($groupedByReference as $reference => $group) {
            $groupData = $group->map(function ($item) {
                return [
                    'reference' => $item->reference,
                    'type' => $item->type,
                    'name' => $item->name,
                    'formatProd' => $item->formatProd,
                    'specification' => $item->specification,
                    'specification2' => $item->specification2,
                    'specification3' => $item->specification3,
                    'origine' => $item->origine,
                    'user_id' => $item->user_id,
                ];
            });

            // Afficher les éléments récupérés dans les logs
            Log::info('Group for reference', [
                'reference' => $reference,
                'items' => $groupData->toArray() // Convertit la collection en tableau
            ]);
        }
        ///////
        $resultCount = $results->count();

        Log::info('Final results', [
            'resultCount' => $resultCount,
            'userId' => $userId,
        ]);

        $prodUsers = $results->pluck('user.id')->unique()->toArray();
        $lowestPricedProduct = $results->min('prix');
        $prodUsersCount = $results->pluck('user')->unique('id')->count();

        Log::info('Additional metrics', [
            'prodUsers' => $prodUsers,
            'lowestPricedProduct' => $lowestPricedProduct,
            'prodUsersCount' => $prodUsersCount,
        ]);
        $produitDims = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->where('user_id', '<>', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        // Récupérer les noms des produits des offres groupées
        $appelOffreGroup = AppelOffreGrouper::where('productName', $keyword)->get();

        $appelOffreGroupcount = $appelOffreGroup->count();

        // Grouper les éléments par code_unique
        $groupedByCodeUnique = $appelOffreGroup->groupBy('codeunique');


        $participantsCount = null;

        $productNames = null;

        $idOffre = null;


        // Parcourir chaque groupe et accéder aux données
        foreach ($groupedByCodeUnique as $codeUnique => $group) {

            $distinctUserIds = AppelOffreGrouper::where('codeunique', $codeUnique)->pluck('user_id')->unique();

            $participantsCount = $distinctUserIds->count();

            $productNames[$codeUnique] = $group->first()->productName;

            $idOffre[$codeUnique] = $group->first()->id;
        }


        return view('biicf.searchAppelOffre', compact('groupedByReference', 'results', 'resultCount', 'keyword', 'prodUsers', 'produitDims', 'prodUsersCount', 'lowestPricedProduct',  'appelOffreGroup', 'appelOffreGroupcount', 'participantsCount', 'groupedByCodeUnique', 'productNames', 'idOffre'));
    }

    public function formAppel(Request $request)
    {
        $keyword = $request->input('keyword');
        $lowestPricedProduct = $request->input('lowestPricedProduct');
        $prodUsers = $request->input('prodUsers');

        $products = $request->input('results');

        // Vérifiez que $prodUsers n'est pas null
        if ($prodUsers) {
            // Si c'est une collection, utilisez toArray(), sinon, assurez-vous que c'est un tableau
            $prodUsers = is_array($prodUsers) ? $prodUsers : (is_object($prodUsers) ? $prodUsers->toArray() : []);
        } else {
            // Si $prodUsers est null, initialisez-le comme un tableau vide
            $prodUsers = [];
        }


        return view('biicf.formappel', compact('lowestPricedProduct', 'prodUsers', 'keyword', 'products'));
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
                'Livraison' => 'required|string',
                'dateTot' => 'required|date',
                'dateTard' => 'required|date',
                'specificity' => 'nullable|string',
                'localite' => 'nullable|string',
                'id_prod' => 'id_prod',
                'image' => 'nullable',
                'prodUsers' => 'required|array', // Assurez-vous que prodUsers est un tableau
            ]);

            $lowestPricedProduct = $request->input('lowestPricedProduct');
            $prodUsers = $request->input('prodUsers');
            $produits = $request->input('products');

            // Vérification que $prodUsers est un tableau non vide
            if (empty($prodUsers)) {
                return redirect()->back()->with('error', 'Aucun utilisateur de produit spécifié.');
            }

            // Générer un code unique une seule fois pour tous les utilisateurs
            $codeUnique = $this->genererCodeAleatoire(10);

            // Boucle sur chaque utilisateur pour envoyer la notification
            foreach ($prodUsers as $prodUser) {
                $data = [
                    'dateTot' => $request->dateTot,
                    'dateTard' => $request->dateTard,
                    'productName' => $request->productName,
                    'quantity' => $request->quantity,
                    'payment' => $request->payment,
                    'Livraison' => $request->livraison,
                    'specificity' => $request->specificity,
                    'localite' => $request->localite,
                    'image' => null, // Gérer l'upload et le stockage de l'image si nécessaire
                    'id_sender' => $userId,
                    'prodUsers' => $prodUser,
                    'produits' => $produits,
                    'lowestPricedProduct' => $lowestPricedProduct,
                    'code_unique' => $codeUnique,
                    'difference' => 'single',
                ];

                // Vérification que toutes les clés nécessaires sont présentes
                $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'image', 'prodUsers', 'lowestPricedProduct', 'difference'];
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
                }
            }

            return redirect()->route('biicf.appeloffre')->with('success', 'Notification envoyée avec succès!');
        } catch (\Exception $e) {
            return redirect()->route('biicf.appeloffre')->with('error', 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
        }
    }

    public function formstoreGroupe(Request $request)
    {
        try {
            // Récupérer l'ID de l'utilisateur connecté
            $userId = Auth::guard('web')->id();
            Log::info('Utilisateur connecté ID:', ['user_id' => $userId]);

            // Générer un code unique une seule fois pour tous les utilisateurs
            $codeunique = $this->genererCodeAleatoire(10);
            Log::info('Code unique généré:', ['codeunique' => $codeunique]);

            // Valider les données de la requête
            $request->validate([
                'productName' => 'required|string',
                'quantity' => 'required|integer',
                'payment' => 'required|string',
                'Livraison' => 'required|string',
                'dateTot' => 'required|date',
                'dateTard' => 'required|date',
                'specificity' => 'nullable|string',
                'localite' => 'nullable|string',
                'id_prod' => 'nullable|string',
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
            $offre->productName = $request->input('productName');
            $offre->lowestPricedProduct = $request->input('lowestPricedProduct');
            $offre->quantity = $request->input('quantity');
            $offre->payment = $request->input('payment');
            $offre->Livraison = $request->input('Livraison');
            $offre->dateTot = $request->input('dateTot');
            $offre->dateTard = $request->input('dateTard');
            $offre->specificity = $request->input('specificity');
            $offre->localite = $request->input('localite');
            $offre->id_prod = $request->input('id_prod');
            $offre->prodUsers = json_encode($request->input('prodUsers'));
            $offre->codeunique = $codeunique;
            $offre->user_id = $userId;

            // Ajouter le chemin de l'image s'il existe
            if ($imagePath) {
                $offre->image = $imagePath;
            }

            // Sauvegarder le modèle
            $offre->save();
            Log::info('Offre enregistrée avec succès.', ['offre_id' => $offre->id]);

            // Créer une nouvelle instance du modèle userquantites
            $quantite = new userquantites();
            $quantite->user_id = $userId;
            $quantite->quantite = $request->input('quantity');
            $quantite->code_unique = $codeunique;
            $quantite->save();
            Log::info('Quantité enregistrée avec succès.', ['quantite_id' => $quantite->id]);



            // Requête pour récupérer les IDs des propriétaires des consommations similaires
            $idsProprietaires = Consommation::where('name', $offre->productName)
                ->where('id_user', '!=', $userId)
                ->where('statuts', 'Accepté')
                ->distinct()
                ->pluck('id_user')
                ->toArray();
            Log::info('IDs des propriétaires récupérés:', ['ids_proprietaires' => $idsProprietaires]);

            // Envoyer une notification à chaque propriétaire
            foreach ($idsProprietaires as $id) {
                $user = User::find($id);
                if ($user) {
                    // Envoyer une notification avec le code unique
                    Notification::send($user, new AOGrouper($codeunique, $offre->id));
                    Log::info('Notification envoyée à l\'utilisateur:', ['user_id' => $id]);
                }
            }

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

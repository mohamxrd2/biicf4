<?php

namespace App\Http\Controllers;

use App\Models\AppelOffreGrouper;
use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use App\Notifications\AppelOffre;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
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

        if ($keyword) {
            $produits->where('name', 'like', '%' . $keyword . '%');
        }

        if ($zoneEconomique) {
            $produits->where('zonecoServ', $zoneEconomique);
        }

        if ($type) {
            $produits->where('type', $type);
        }

        $results = $produits->where('user_id', '<>', $userId)->get();
        $resultCount = $results->count();

        $prodUsers = $results->pluck('user.id')->unique()->toArray();

        $lowestPricedProduct = $results->min('prix');;

        $prodUsersCount = $results->pluck('user')->unique('id')->count();

        $produitDims = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->where('user_id', '<>', $userId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);


        // Récupérer les noms des produits des offres groupées
        $appelOffreGroup = AppelOffreGrouper::whereNotNull('productName')->get();
        $appelOffreGroupcount = AppelOffreGrouper::count(); // Compte le nombre total d'offres groupées



        return view('biicf.searchAppelOffre', compact('results', 'resultCount', 'keyword', 'prodUsers', 'produitDims', 'prodUsersCount', 'lowestPricedProduct',  'appelOffreGroup', 'appelOffreGroupcount'));
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
            return response()->json(['error' => 'Offre non trouvée'], 404);
        }



        // Récupérer les variables
        $codesUniques = $appelOffreGroup->codeunique;
        $dateTot = $appelOffreGroup->dateTot;
        $dateTard = $appelOffreGroup->dateTard;
        $productName = $appelOffreGroup->productName;
        $quantity = $appelOffreGroup->quantity;
        $payment = $appelOffreGroup->payment;
        $livraison = $appelOffreGroup->livraison;
        $payment = $appelOffreGroup->payment;
        $specificity = $appelOffreGroup->specificity;

        // Récupérer les utilisateurs associés à l'offre groupée
        $prodUsersJson = $appelOffreGroup->prodUsers;

        // Décoder la chaîne JSON en tableau PHP
        $prodUsers = json_decode($prodUsersJson, true);

        // Récupérer la date la plus ancienne pour le code unique
        $datePlusAncienne = AppelOffreGrouper::where('codeunique', $codesUniques)->min('created_at');

        // Ajouter une heure à l'heure actuelle pour obtenir le temps écoulé
        $tempEcoule = Carbon::now()->addHour();

        // Calculer la somme des quantités pour le code unique actuel
        $sumquantite = AppelOffreGrouper::where('codeunique', $codesUniques)->sum('quantity');

        // Ajouter 0 heures à la date la plus ancienne, s'il y en a une
        $tempsEcoule = $datePlusAncienne ? Carbon::parse($datePlusAncienne)->addHours(1) : null;

        // Vérifier si le temps est écoulé
        $isTempsEcoule = $tempsEcoule && $tempsEcoule->isPast();


        // Compter le nombre distinct d'utilisateurs pour le code unique
        $appelOffreGroupcount = AppelOffreGrouper::where('codeunique', $codesUniques)
            ->distinct('user_id')
            ->count('user_id');

        // Boucle sur chaque utilisateur pour envoyer la notification
        foreach ($prodUsers as $prodUser) {
            $data = [
                'dateTot' => $dateTot,
                'dateTard' => $dateTard,
                'productName' => $productName,
                'quantity' => $quantity,
                'payment' => $payment,
                'Livraison' => $livraison,
                'specificity' => $specificity,
                'image' => null, // Gérer l'upload et le stockage de l'image si nécessaire
                'id_sender' => $userId,
                'prodUsers' => $prodUser,
                'sumquantite' => $sumquantite,
                'code_unique' => $codesUniques,
            ];

            // Vérification que toutes les clés nécessaires sont présentes
            $requiredKeys = ['dateTot', 'dateTard', 'productName', 'quantity', 'payment', 'Livraison', 'specificity', 'image', 'prodUsers', 'code_unique'];
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

            // Création du commentaire
            Comment::create([
                'prixTrade' => null,
                'id_trader' => $prodUser,
                'code_unique' => $codesUniques,
                'id_prod' => null
            ]);
        }

        // Passer les variables à la vue (si nécessaire)
        return view('biicf.ajoutoffre', compact('userId', 'appelOffreGroup', 'datePlusAncienne', 'tempEcoule', 'sumquantite', 'appelOffreGroupcount'));
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

        return redirect()->back()->with('success', 'offre soumis avec success');
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

                Comment::create([
                    'prixTrade' => null,
                    'id_trader' => $prodUser,
                    'code_unique' => $codeUnique,
                    'id_prod' => null
                ]);
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

            // Générer un code unique une seule fois pour tous les utilisateurs
            $codeunique = $this->genererCodeAleatoire(10);

            // Validate the request data
            $request->validate([
                'productName' => 'required|string',
                'quantity' => 'required|integer',
                'payment' => 'required|string',
                'Livraison' => 'required|string',
                'dateTot' => 'required|date',
                'dateTard' => 'required|date',
                'specificity' => 'nullable|string',
                'id_prod' => 'nullable|string',
                'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'prodUsers' => 'required|array',
            ]);

            // Handle file upload
            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('images', 'public');
            }

            // Create a new instance of the model
            $offre = new AppelOffreGrouper();

            // Assign values
            $offre->productName = $request->input('productName');
            $offre->lowestPricedProduct = $request->input('lowestPricedProduct');
            $offre->quantity = $request->input('quantity');
            $offre->payment = $request->input('payment');
            $offre->Livraison = $request->input('Livraison');
            $offre->dateTot = $request->input('dateTot');
            $offre->dateTard = $request->input('dateTard');
            $offre->specificity = $request->input('specificity');
            $offre->id_prod = $request->input('id_prod');
            $offre->prodUsers = json_encode($request->input('prodUsers'));
            $offre->codeunique = $codeunique;
            $offre->user_id = $userId;

            // Add the image path if it exists
            if ($imagePath) {
                $offre->image = $imagePath;
            }

            // Save the model
            $offre->save();

            return redirect()->route('biicf.appeloffre')->with('success', 'Notification envoyée avec succès!');
        } catch (\Exception $e) {
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
    public function comment(Request $request)
    {
        // Validation des données du formulaire
        $request->validate([
            'prixTrade' => 'required|integer',
            'id_trader' => 'required|integer|exists:users,id',
            'code_unique' => 'required|string|exists:comments,code_unique',
        ]);


        // Création du commentaire
        Comment::create([
            'prixTrade' => $request->input('prixTrade'),
            'id_trader' => $request->input('id_trader'),
            'code_unique' => $request->input('code_unique'),
        ]);

        // Redirection avec un message de succès
        return redirect()->back();
    }
}

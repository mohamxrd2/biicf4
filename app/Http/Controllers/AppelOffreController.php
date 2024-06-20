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
    //

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
        $appelOffreGroup = AppelOffreGrouper::pluck('productName');

        // Récupérer tous les codes uniques
        $codesUniques = AppelOffreGrouper::pluck('codeunique');

        // Initialiser un tableau pour stocker les dates les plus anciennes
        $datesPlusAnciennes = [];

        // Itérer sur chaque code unique pour récupérer la date la plus ancienne
        foreach ($codesUniques as $codeUnique) {
            $datePlusAncienne = AppelOffreGrouper::where('codeunique', $codeUnique)->min('created_at');
            $datesPlusAnciennes[$codeUnique] = $datePlusAncienne;
        }

        // Ajouter une heure à l'heure actuelle pour obtenir le temps écoulé
        $tempEcoule = Carbon::now()->addHour();



        return view('biicf.searchAppelOffre', compact('results', 'resultCount', 'keyword', 'prodUsers', 'produitDims', 'prodUsersCount', 'lowestPricedProduct', 'appelOffreGroup', 'datePlusAncienne', 'tempEcoule'));
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

    public function detailoffre(Request $request)
    {
        try {
            $userId = Auth::guard('web')->id();


            return view('biicf.ajoutoffre');
        } catch (\Exception $e) {
            return redirect()->route('biicf.appeloffre')->with('error', 'Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
        }
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

            // Prepare data for insertion
            $data = $request->only([
                'productName',
                'quantity',
                'payment',
                'Livraison',
                'dateTot',
                'dateTard',
                'specificity',
                'id_prod',
                'prodUsers'
            ]);

            // Add the image path if it exists
            if ($imagePath) {
                $data['image'] = $imagePath;
            }

            // Ensure prodUsers is stored as JSON
            $data['prodUsers'] = json_encode($data['prodUsers']);

            // Add the unique code
            $data['codeunique'] = $codeunique;

            // Insert data into the table
            AppelOffreGrouper::create($data);

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

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Consommation;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

    public function listUserAdmin()
    {
        $users = User::with('admin')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $userCount = User::count();

        //Agent//////

        //  l'agent connecté
        $adminId = Auth::guard('admin')->id();
        // utilisateurs ayant le même admin_id que l'agent
        $userAgent = User::where('admin_id', $adminId)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        // Nombre total d'utilisateurs ayant le même admin_id que l'agent
        $userAgentcount = User::where('admin_id', $adminId)->count();

        return view('admin.client', compact('users', 'userCount', 'userAgent', 'userAgentcount'));
    }

    public function destroyUser(User $user)
    {
        $user->delete();

        return back()->with('success', 'Utilisateur supprimé avec succès.');
    }

    public function createPageAdmin()
    {

        return view('admin.addclient');
    }

    public function createUserAdmin(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'repeat-password' => 'required|string|same:password',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required',
            'local' => 'required',
            'adress_geo' => 'required',
            'proximity' => 'required',
        ], [
            'email.unique' => 'Email deja utlisé',
            'name.required' => 'Le champ nom est requis.',
            'username.required' => 'Le champ nom d\'utilisateur est requis.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'repeat-password.required' => 'Le champ confirmation du mot de passe est requis.',
            'repeat-password.same' => 'Les mots de passe ne correspondent pas.',
            'email.required' => 'Le champ email est requis.',
            'email.email' => 'L\'adresse email doit être valide.',
            'phone.required' => 'Le champ téléphone est requis.',
            'local.required' => 'Le champ localité est requis.',
            'adress_geo.required' => 'Le champ adresse géographique est requis.',
            'proximity.required' => 'Le champ zone d\'activité est requis.',
        ]);

        try {
            $user = new User();
            $user->name = $validatedData['name'] . ' ' . $request->input('last-name');
            $user->username = $validatedData['username'];
            $user->password = Hash::make($validatedData['password']);
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->local_area = $validatedData['local'];
            $user->address = $validatedData['adress_geo'];
            $user->active_zone = $validatedData['proximity'];
            $user->actor_type = $request->input('user_type');
            $user->gender = $request->input('user_sexe');
            $user->age = $request->input('user_age');
            $user->social_status = $request->input('user_status');
            $user->company_size = $request->input('user_comp_size');
            $user->service_type = $request->input('user_serv');
            $user->organization_type = $request->input('user_orgtyp');
            $user->second_organization_type = $request->input('user_orgtyp2');
            $user->communication_type = $request->input('user_com');
            $user->mena_type = $request->input('user_mena1');
            $user->mena_status = $request->input('user_mena2');
            $user->sector = $request->input('sector_activity');
            $user->industry = $request->input('industry');
            $user->construction = $request->input('building_type');
            $user->commerce = $request->input('commerce_sector');
            $user->services = $request->input('transport_sector');
            $user->country = $request->input('country');
            $user->parrain = $request->input('parrain');

            $adminId = Auth::guard('admin')->id();

            // Vérification si l'admin est authentifié
            if ($adminId) {
                //generer et garder le token de verification
                $user->email_verified_at = now(); //marquer l'email comme verifier
                $user->admin_id = $adminId;
                $user->save();

                $wallet = new Wallet();
                $wallet->user_id = $user->id;
                $wallet->balance = 0; // Solde initial
                $wallet->save();

                //envoi du couriel au nouveau client
                // $user->sendEmailVerificationNotification();

                return redirect()->route('clients.create')->with('success', 'Client ajouté avec succès!');
            } else {
                // L'admin n'est pas authentifié
                return back()->withErrors(['error' => 'L\'administrateur n\'est pas authentifié.']);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    public function show($username)
    {
        // Récupérer les détails du client en fonction de son nom d'utilisateur
        $user = User::with('admin')->where('username', $username)->firstOrFail();



        $wallet = Wallet::where('user_id', $user->id)->first();

        // Récupérer tous les produits de service associés à cet utilisateur
        $produitsServices = ProduitService::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $produitCount = $produitsServices->count();


        $consommations = Consommation::where('id_user', $user->id)->get();

        $consCount = $consommations->count();

        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where('sender_user_id', $user->id)
            ->orWhere('receiver_user_id', $user->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $transaCount = $transactions->count();



        // Passer les détails du client à la vue
        return view('admin.clientShow', compact('user', 'wallet', 'produitsServices', 'produitCount', 'consommations', 'consCount', 'transactions', 'transaCount'));
    }

    public function pubShow($id)
    {
        $produits = ProduitService::find($id);


        return view('admin.pubVerif', compact('produits'));
    }

    public function etat(Request $request, $id)
    {
        // Trouver le produit en fonction de l'ID
        $produits = ProduitService::find($id);

        // Vérifier si le produit a été trouvé
        if ($produits) {
            // Vérifier l'action à effectuer (accepter ou refuser)
            $action = $request->input('action');

            // Modifier l'attribut "statut" en fonction de l'action
            if ($action === 'accepter') {
                $produits->statuts = 'Accepté';
            } elseif ($action === 'refuser') {
                $produits->statuts = 'Refusé';
            } else {
                // Gérer une action invalide si nécessaire
                return back()->with('error', 'Action invalide.');
            }

            // Enregistrer les modifications dans la base de données
            $produits->save();

            // Retourner une réponse ou effectuer toute autre action nécessaire
            return back()->with('success', 'Status du produit modifier avec succès !');
        } else {
            // Le produit n'a pas été trouvé, retourner une réponse avec un code d'erreur
            return back()->with('error', 'Le produit n\'a pas été trouvé.');
        }
    }
    public function consoShow($id)
    {
        $consommations = Consommation::find($id);


        return view('admin.consoVerif', compact('consommations'));
    }

    public function consoEtat(Request $request, $id)
    {
        // Trouver le produit en fonction de l'ID
        $Consommations = Consommation::find($id);

        // Vérifier si le produit a été trouvé
        if ($Consommations) {
            // Vérifier l'action à effectuer (accepter ou refuser)
            $action = $request->input('action');

            // Modifier l'attribut "statut" en fonction de l'action
            if ($action === 'accepter') {
                $Consommations->statuts = 'Accepté';
            } elseif ($action === 'refuser') {
                $Consommations->statuts = 'Refusé';
            } else {
                // Gérer une action invalide si nécessaire
                return back()->with('error', 'Action invalide.');
            }

            // Enregistrer les modifications dans la base de données
            $Consommations->save();

            // Retourner une réponse ou effectuer toute autre action nécessaire
            return back()->with('success', 'Produit ou service ajouté avec succès!');
        } else {
            // Le produit n'a pas été trouvé, retourner une réponse avec un code d'erreur
            return back()->with('error', 'Le produit n\'a pas été trouvé.');
        }
    }


    public function storePub(Request $request)
    {
        $userId = $request->input('user_id');


        $validatedData = $request->validate([
            'type' => 'required|string|in:produits,services', // Type doit être soit 'product' soit 'service'
            'name' =>  'required|string|max:255',
            'conditionnement' => $request->type == 'produits' ? 'required|string|max:255' : 'nullable|string|max:255',
            'format' => $request->type == 'produits' ? 'required|string' : 'nullable|string',
            'qteProd_min' => $request->type == 'produits' ? 'required|string' : 'nullable|string',
            'qteProd_max' => $request->type == 'produits' ? 'required|string' : 'nullable|string',
            'prix' => $request->type == 'produits' ? 'required' : 'nullable', // Prix requis uniquement pour les produits
            'livraison' =>  $request->type == 'produits' ? 'required|string|in:oui,non' : 'nullable|string|in:oui,non',
            'qualification'  => $request->type == 'services' ? 'required|string' : 'nullable|string',
            'specialite' => $request->type == 'services' ? 'required|string' : 'nullable|string',
            'qte_service' => $request->type == 'services' ? 'required|string' : 'nullable|string', // Quantité de service requise uniquement pour les services
            'ville' => 'required|string',
            'commune' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,heic,heif', // Modifier les types de fichiers acceptés et la taille maximale si nécessaire
            'image2' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic,heif',
            'image3' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic,heif',
            'image4' => 'nullable|image|mimes:jpeg,png,jpg,gif,heic,heif',
            
            'description' => 'required|string'
        ], [
            // Messages d'erreur personnalisés
            'type.required' => 'Le type est requis.',
            'type.in' => 'Le type doit être soit "produit" soit "service".',
            'name.required' => 'Le nom est requis.',
            'name.max' => 'Le nom ne doit pas dépasser 255 caractères.',
            'conditionnement.required' => 'Le conditionnement est requis pour les produits.',
            'conditionnement.max' => 'Le conditionnement ne doit pas dépasser 255 caractères.',
            'format.required' => 'Le format est requis pour les produits.',
            'qteProd_min.required' => 'La quantité minimale est requise pour les produits.',
            'qteProd_max.required' => 'La quantité maximale est requise pour les produits.',
            'prix.required' => 'Le prix est requis pour les produits.',
            'livraison.required' => 'La livraison est requise pour les produits.',
            'qualification.required' => 'La qualification est requise pour les services.',
            'specialite.required' => 'La spécialité est requise pour les services.',
            'qte_service.required' => 'La quantité de service est requise pour les services.',

            'ville.required' => 'La ville est requise.',
            'commune.required' => 'La commune est requise.',


            'image.image' => 'Le fichier doit être une image.',
            'image.required' => 'La photo 1 est obligatoire',
            'image.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.',


            'image2.image' => 'Le fichier doit être une image2.',
            'image2.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.2',


            'image3.image' => 'Le fichier doit être une image.3',
            'image3.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.3',


            'image4.image' => 'Le fichier doit être une image.4',
            'image4.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.4',


            'description.required' => 'La description est requise.'
        ]);

        try {
            $produitsServices = new ProduitService();
            $produitsServices->type = $validatedData['type'];
            $produitsServices->name = $validatedData['name'];
            $produitsServices->condProd = $validatedData['conditionnement'];
            $produitsServices->formatProd = $validatedData['format'];
            $produitsServices->qteProd_min = $validatedData['qteProd_min'];
            $produitsServices->qteProd_max = $validatedData['qteProd_max'];
            $produitsServices->prix = $validatedData['prix'];
            $produitsServices->LivreCapProd = $validatedData['livraison'] ?? null;
            $produitsServices->qalifServ = $validatedData['qualification'] ?? null;
            $produitsServices->sepServ = $validatedData['specialite'];
            $produitsServices->qteServ = $validatedData['qte_service'];

            $produitsServices->villeServ = $validatedData['ville'];
            $produitsServices->comnServ = $validatedData['commune'];
            $produitsServices->desrip = $validatedData['description'];
            $produitsServices->user_id = $userId; // Ajout de l'ID de l'utilisateur


            if ($request->hasFile('image') || $request->hasFile('image2') || $request->hasFile('image3') || $request->hasFile('image4')) {
                $path = 'post/';

                // Vérifiez si la première image est téléchargée
                if ($request->hasFile('image')) {
                    $image = $request->file('image');
                    $imageName = time() . '_1.' . $image->getClientOriginalExtension();
                    $image->move($path, $imageName);
                    $produitsServices->photoProd1 = $path . $imageName;
                }

                // Vérifiez si la deuxième image est téléchargée
                if ($request->hasFile('image2')) {
                    $image2 = $request->file('image2');
                    $imageName2 = time() . '_2.' . $image2->getClientOriginalExtension();
                    $image2->move($path, $imageName2);
                    $produitsServices->photoProd2 = $path . $imageName2;
                }
                // Vérifiez si la deuxième image est téléchargée
                if ($request->hasFile('image3')) {
                    $image3 = $request->file('image3');
                    $imageName3 = time() . '_3.' . $image3->getClientOriginalExtension();
                    $image3->move($path, $imageName3);
                    $produitsServices->photoProd3 = $path . $imageName3;
                }
                // Vérifiez si la deuxième image est téléchargée
                if ($request->hasFile('image4')) {
                    $image4 = $request->file('image4');
                    $imageName4 = time() . '_4.' . $image4->getClientOriginalExtension();
                    $image4->move($path, $imageName4);
                    $produitsServices->photoProd4 = $path . $imageName4;
                }
            }

            $produitsServices->save();



            return back()->with('success', 'Produit ou service ajouté avec succès!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }
    public function storeCons(Request $request)
    {
        $userId = $request->input('user_id');


        $validatedData = $request->validate(
            [
                'nameC' => 'required|string|max:255',
                'type' => 'required|string|in:produits,services',
                'conditionnementC' => $request->type == 'produits' ? 'required|string|max:255' : 'nullable|string|max:255',
                'formatC' => $request->type == 'produits' ? 'required|string' : 'nullable|string',
                'qteC' => $request->type == 'produits' ? 'required|string' : 'nullable|string',
                'prixC' => $request->type == 'produits' ? 'required' : 'nullable',
                'frequenceC' => 'required|string', // Attention à l'espace dans le nom du champ
                'jour_achat' => 'required|string',
                'qualificationC' => $request->type == 'services' ? 'required|string' : 'nullable|string',
                'specialité' => $request->type == 'services' ? 'required|string' : 'nullable|string',
                'desriptionC' => 'required|string',
                'zone_activité' => 'required|string',
                'villeC' => 'required|string',
            ],
            [
                'nameC.required' => 'Le nom de la consommation est obligatoire.',
                'type.required' => 'Le type est requis.',
                'type.in' => 'Le type doit être soit "product" soit "service".',
                'conditionnementC.required' => 'Le conditionnement est requis pour les produits.',
                'formatC.required' => 'Le format est requis pour les produits.',
                'qteC.required' => 'La quantité est requise pour les produits.',
                'prixC.required' => 'Le prix est requis pour les produits.',
                'frequenceC.required' => 'La fréquence d\'achat est requise.',
                'jour_achat.required' => 'Le jour d\'achat est requis.',
                'qualificationC.required' => 'La qualification est requise pour les services.',
                'specialité.required' => 'La spécialité est requise pour les services.',
                'desriptionC.required' => 'La description est requise.',
                'zone_activité.required' => 'La zone d\'activité est requise.',
                'villeC.required' => 'La ville est requise.',
            ]
        );

        try {
            // Créer une nouvelle instance de Consommation avec les données validées
            $consommation = new Consommation();
            $consommation->name = $validatedData['nameC'];
            $consommation->type = $validatedData['type'];
            $consommation->conditionnement = $validatedData['conditionnementC'];
            $consommation->format = $validatedData['formatC'];
            $consommation->qte = $validatedData['qteC'];
            $consommation->prix = $validatedData['prixC'];
            $consommation->frqce_cons = $validatedData['frequenceC']; // Attention à l'espace dans le nom du champ
            $consommation->jourAch_cons = $validatedData['jour_achat'];
            $consommation->qualif_serv = $validatedData['qualificationC'];
            $consommation->specialité = $validatedData['specialité'];
            $consommation->description = $validatedData['desriptionC'];
            $consommation->zoneAct = $validatedData['zone_activité'];
            $consommation->villeCons = $validatedData['villeC'];
            $consommation->id_user = $userId; // Ajout de l'ID de l'utilisateur


            // Enregistrer la nouvelle consommation dans la base de données
            $consommation->save();

            // Rediriger avec un message de succès
            return back()->with('success', 'Consommation ajoutée avec succès!');
        } catch (\Exception $e) {
            dd($e->getMessage());

            // En cas d'erreur, afficher un message d'erreur et rediriger vers la page précédente
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }
    public function editAgent($username)
    {
        $user = User::with('admin')->where('username', $username)->firstOrFail();

        // Récupérer tous les agents de type 'agent' et les ordonner par date de création
        $agents = Admin::where('admin_type', 'agent')->orderBy('created_at', 'DESC')->get();

        // Récupérer le nombre total d'agents
        $totalAgents = $agents->count();

        foreach ($agents as $agent) {
            // Récupérer le nombre d'utilisateurs associés à cet agent
            $userCount = $agent->users()->count();
            // Ajouter le nombre d'utilisateurs à l'agent
            $agent->userCount = $userCount;
        }

        return view('admin.editagent', compact('user', 'agents', 'totalAgents'));
    }

    public function updateAdmin(Request $request, $username)
    {
        // Récupérer l'utilisateur
        $user = User::where('username', $username)->firstOrFail();

        // Mettre à jour l'administrateur associé à l'utilisateur
        $user->admin_id = $request->admin_id;
        $user->save();

        // Rediriger avec un message de succès
        return back()->with('success', 'Administrateur mis à jour avec succès.');
    }


    public function createPageBiicf()
    {
        return view('auth.signup');
    }
    public function createUserBiicf(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8',
            'repeat-password' => 'required|string|same:password',
            'email' => 'required|email|max:255|unique:users',
            'phone' => 'required',
            'local' => 'required',
            'adress_geo' => 'required',
            'proximity' => 'required',
        ], [
            'email.unique' => 'Email deja utlisé',
            'name.required' => 'Le champ nom est requis.',
            'username.required' => 'Le champ nom d\'utilisateur est requis.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'repeat-password.required' => 'Le champ confirmation du mot de passe est requis.',
            'repeat-password.same' => 'Les mots de passe ne correspondent pas.',
            'email.required' => 'Le champ email est requis.',
            'email.email' => 'L\'adresse email doit être valide.',
            'phone.required' => 'Le champ téléphone est requis.',
            'local.required' => 'Le champ localité est requis.',
            'adress_geo.required' => 'Le champ adresse géographique est requis.',
            'proximity.required' => 'Le champ zone d\'activité est requis.',
        ]);

        try {
            $user = new User();
            $user->name = $validatedData['name'] . ' ' . $request->input('last-name');
            $user->username = $validatedData['username'];
            $user->password = Hash::make($validatedData['password']);
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->local_area = $validatedData['local'];
            $user->address = $validatedData['adress_geo'];
            $user->active_zone = $validatedData['proximity'];
            $user->actor_type = $request->input('user_type');
            $user->gender = $request->input('user_sexe');
            $user->age = $request->input('user_age');
            $user->social_status = $request->input('user_status');
            $user->company_size = $request->input('user_comp_size');
            $user->service_type = $request->input('user_serv');
            $user->organization_type = $request->input('user_orgtyp');
            $user->second_organization_type = $request->input('user_orgtyp2');
            $user->communication_type = $request->input('user_com');
            $user->mena_type = $request->input('user_mena1');
            $user->mena_status = $request->input('user_mena2');
            $user->sector = $request->input('sector_activity');
            $user->industry = $request->input('industry');
            $user->construction = $request->input('building_type');
            $user->commerce = $request->input('commerce_sector');
            $user->services = $request->input('transport_sector');
            $user->country = $request->input('country');

            $user->save();

            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->balance = 0; // Solde initial
            $wallet->save();

            //envoi du couriel au nouveau client
            $user->sendEmailVerificationNotification();

            return redirect()->route('biicf.login')->with('success', 'Client ajouté avec succès, veillez confirmer votre email!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    public function showProfile()
    {
        $userId = Auth::guard('web')->id();
        $user = User::with('parrain')->find($userId);

        // Récupérer le parrain en tant qu'objet User
        $parrain = User::where('id', $user->parrain)->first();

        return view('biicf.profile', compact('user', 'parrain'));
    }

    public function updateProfile(Request $request, User $user)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:admins,username,' . $user->id,
            'phonenumber' => 'required|string',
        ], [
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
        ]);

        try {
            // Mettre à jour les données de l'administrateur
            $user->name = $validatedData['name'];
            $user->username = $validatedData['username'];
            $user->phone = $validatedData['phonenumber'];
            $user->save();

            return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('erroruser', 'Une erreur est survenue lors de la mise à jour du profil.');
        }
    }

    public function updatePassword(Request $request, User $user)
    {
        // Valider les données du formulaire avec les messages d'erreur personnalisés
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|same:new_password',
        ], [
            'new_password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'new_password_confirmation.same' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        // Vérifier si le mot de passe actuel est correct
        if (!Hash::check($validatedData['current_password'], $user->password)) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }

        // Mettre à jour le mot de passe de l'user
        $user->password = Hash::make($validatedData['new_password']);
        $user->save();

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updateProfilePhoto(Request $request, $userId)
    {
        // Récupérer l'instance de l'admin
        $user = User::find($userId);

        if (!$user) {
            return back()->withErrors(['error' => 'Administrateur non trouvé.'])->withInput();
        }

        // Valider les données du formulaire
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif', // Modifier les types de fichiers acceptés et la taille maximale si nécessaire
        ], [
            'image.image' => 'Le fichier doit être une image.',
            'image.required' => 'La photo est obligatoire.',
            'image.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Stockez l'image dans le dossier 'public/post'
            $path = 'post/';
            $image->move($path, $imageName);

            // Enregistrez le nom de l'image dans la base de données

        }

        try {
            $user->photo = $request->hasFile('image') ? $path . $imageName : null;
            $user->save();


            return back()->with('success', 'Photo de profil mise à jour avec succès!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour de la photo de profil.'])->withInput();
        }
    }
}

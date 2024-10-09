<?php

namespace App\Http\Controllers;

use App\Models\Investisseur;
use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Consommation;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

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
            'address' => 'required',
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
            $user->address = $request->input('address');
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
        return view('admin.pubVerif', compact('id'));
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

        return view('admin.consoVerif',  compact('id'));
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

    // public $countries = [];

    // public function fetchCountries()
    // {
    //     try {
    //         $response = Http::get('https://restcountries.com/v3.1/all');
    //         $this->countries = collect($response->json())->pluck('name.common')->sort()->toArray();
    //     } catch (\Exception $e) {
    //         // Handle the error (e.g., log it, show an error message)
    //         $this->countries = [];
    //     }
    // }

    public function createPageBiicf()
    {
        // $this->fetchCountries();

        return view('auth.signup');
    }
    public function createUserBiicf(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'last-name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'repeat-password' => 'required|string|same:password',
            'user_type' => 'required|string',
            'invest_type' => 'required|string',
            'investisement' => 'required|string',
            'phone' => 'required|unique:users',
            'continent' => 'required|string',
            'sous_region' => 'required|string',
            'country' => 'required|string',
            'departement' => 'required|string',
            'ville' => 'required|string',
            'commune' => 'required|string',
            'parrain' => 'nullable', // Ajout du champ parrain


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
        ]);
        // dd($validatedData);
        try {
            $user = new User();
            $user->name = $validatedData['name'] . ' ' . $request->input('last-name');
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
            $user->email_verified_at = now();
            $user->password = Hash::make($validatedData['password']);
            $user->actor_type = $request->input('user_type');
            $user->invest_type = $request->input('invest_type');
            $user->investissement = $request->input('investisement');
            $user->phone = $validatedData['phone'];
            $user->continent = $request->input('continent');
            $user->sous_region = $request->input('sous_region');
            $user->country = $request->input('country');
            $user->departe = $request->input('departement');
            $user->ville = $request->input('ville');
            $user->commune = $request->input('commune');
            $user->parrain = $request->input('parrain');
            $user->save();




            $wallet = new Wallet();
            $wallet->user_id = $user->id;
            $wallet->balance = 0; // Solde initial
            $wallet->save();

            // Après la création de l'utilisateur, on envoie le SMS de vérification
            $this->sendSmsVerification($user->phone);

            //envoi du couriel au nouveau client
            // $user->sendEmailVerificationNotification();
            // $user->email_verified_at = now();

            // return redirect()->route('biicf.login')->with('success', 'Client ajouté avec succès, veillez confirmer votre email!');
            // return redirect()->route('biicf.login')->with('success', 'Création du compte avec succès, Connectez-vous!');
            // Récupérer les informations Twilio depuis le fichier .env
            return redirect()->route('verify.phone', ['phone' => $user->phone])->with('success', 'Code de vérification envoyé par SMS. Veuillez vérifier votre numéro.');
        } catch (\Exception $e) {
            // dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    private function sendSmsVerification($phoneNumber)
    {
        // Récupérer les informations Twilio depuis le fichier .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $verifyServiceId = env('TWILIO_VERIFY_SERVICE_ID');
        $twilio = new Client($sid, $token);

        try {
            // Créer la vérification via le service Twilio Verify
            $verification = $twilio->verify->v2->services($verifyServiceId)
                ->verifications
                ->create($phoneNumber, "sms");

            // Retourner une réponse indiquant que le SMS a été envoyé (facultatif)
            // return response()->json([
            //     'message' => 'SMS de vérification envoyé',
            //     'sid' => $verification->sid
            // ], 200);
        } catch (\Exception $e) {
            // Gérer les erreurs
            return response()->json([
                'message' => 'Erreur lors de l\'envoi du SMS de vérification',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function showPhoneVerificationForm()
    {
        return view('auth.verify-phone');
    }

    public function verifyPhoneCode(Request $request)
    {
        // Validation du code de vérification
        $request->validate([
            'verification_code' => 'required|array',  // Le code est reçu sous forme de tableau
            'verification_code.*' => 'required|string|max:1',  // Chaque élément est une chaîne de 1 caractère
            'phone' => 'required|string'
        ]);

        // Fusionner le tableau en une seule chaîne de caractères
        $verificationCode = implode('', $request->input('verification_code'));

        // Récupérer les informations Twilio depuis le fichier .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $verifyServiceId = env('TWILIO_VERIFY_SERVICE_ID');
        $twilio = new Client($sid, $token);

        try {
            // Vérifier le code via Twilio Verify
            $verificationCheck = $twilio->verify->v2->services($verifyServiceId)
                ->verificationChecks
                ->create([
                    'to' => $request->phone,
                    'code' => $verificationCode
                ]);

            if ($verificationCheck->status == 'approved') {
                // Récupérer l'utilisateur via son numéro de téléphone
                $user = User::where('phone', $request->phone)->firstOrFail();

                // Insérer les informations dans la table 'investisseurs'
                $investisseur = new Investisseur();
                $investisseur->nom = $user->name;  // Le nom complet de l'utilisateur
                $investisseur->prenom = $user->username;  // Le prénom, ici j'utilise le username
                $investisseur->tranche = $user->investissement;  // L'investissement de l'utilisateur
                $investisseur->invest_type = $request->invest_type;
                $investisseur->user_id = $user->id;  // Associer l'utilisateur à l'investisseur
                $investisseur->save();  // Sauvegarder les informations dans la table

                // Rediriger l'utilisateur avec un message de succès
                return redirect()->route('biicf.login')->with('success', 'Votre numéro a été vérifié avec succès et vous avez été ajouté en tant qu\'investisseur !');
            } else {
                // Si le code est invalide, renvoyer une erreur
                return back()->withErrors(['verification_code' => 'Code de vérification incorrect.']);
            }
        } catch (\Exception $e) {
            // Gérer les erreurs en cas de problème avec Twilio
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la vérification du code.']);
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

    public function livraisonliste()
    {
        return view('admin.demande');
    }
    public function detaillivraison($id)
    {
        return view('admin.detail-livraison', compact('id'));
    }
    public function detailpsap($id)
    {
        return view('admin.detaill-psap', compact('id'));
    }

    public function detailprojet($id)
    {
        return view ('finance.detailprojet', compact('id'));
    }
}

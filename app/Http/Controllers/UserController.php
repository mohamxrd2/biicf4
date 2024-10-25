<?php

namespace App\Http\Controllers;

use App\Models\Investisseur;
use App\Models\User;
use App\Models\Admin;
use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\Cfa;
use App\Models\Coi;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\Consommation;
use App\Models\Crp;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
            'name.required' => 'Le champ nom est requis.',
            'last-name.required' => 'Le champ prénom est requis.',
            'username.required' => 'Le champ nom d\'utilisateur est requis.',
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'email.required' => 'Le champ email est requis.',
            'email.email' => 'L\'adresse email doit être valide.',
            'email.unique' => 'Cet email est déjà utilisé.',
            'password.required' => 'Le champ mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'repeat-password.required' => 'Le champ confirmation du mot de passe est requis.',
            'repeat-password.same' => 'Les mots de passe ne correspondent pas.',
            'user_type.required' => 'Le type d\'utilisateur est requis.',
            'invest_type.required' => 'Le type d\'investissement est requis.',
            'investisement.required' => 'Le champ investissement est requis.',
            'phone.required' => 'Le champ téléphone est requis.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'continent.required' => 'Le champ continent est requis.',
            'sous_region.required' => 'Le champ sous-région est requis.',
            'country.required' => 'Le champ pays est requis.',
            'departement.required' => 'Le champ département est requis.',
            'ville.required' => 'Le champ ville est requis.',
            'commune.required' => 'Le champ commune est requis.',
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


            // Après la création de l'utilisateur, on envoie le SMS de vérification
            // $this->sendSmsVerification($user->phone);
            $user->save();

            //envoi du couriel au nouveau client
            // $user->sendEmailVerificationNotification();
            // $user->email_verified_at = now();

            // return redirect()->route('biicf.login')->with('success', 'Client ajouté avec succès, veillez confirmer votre email!');
            // return redirect()->route('biicf.login')->with('success', 'Création du compte avec succès, Connectez-vous!');
            // Récupérer les informations Twilio depuis le fichier .env
            return redirect()->route('verify.phone', ['phone' => $user->phone])->with('success', 'Code de vérification envoyé par SMS. Veuillez vérifier votre numéro.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    private function sendSmsVerification($phoneNumber)
    {
        // Récupération des informations Twilio depuis le fichier .env
        $sid = env('TWILIO_SID');
        $token = env('TWILIO_TOKEN');
        $verifyServiceId = env('TWILIO_VERIFY_SERVICE_ID');
        $twilio = new Client($sid, $token);

        try {
            // Création de la vérification via le service Twilio Verify
            $twilio->verify->v2->services($verifyServiceId)
                ->verifications
                ->create($phoneNumber, "sms");
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS: ' . $e->getMessage());
            return response()->json(['message' => 'Erreur lors de l\'envoi du SMS de vérification', 'error' => $e->getMessage()], 400);
        }
    }

    public function verifyPhoneCode(Request $request)
    {
        // Ajout d'un log pour indiquer que la vérification commence
        Log::info('Début de la vérification du code OTP.', ['phone' => $request->phone]);

        // Validation du code de vérification
        $validatedData = $request->validate([
            'verification_code' => 'required|array|min:6|max:6', // Le code doit être un tableau de 6 éléments
            'verification_code.*' => 'required|string|max:1', // Chaque champ doit être un caractère
            'phone' => 'required|string'
        ]);

        // Ajout d'un log pour vérifier les données validées
        Log::info('Données validées pour la vérification.', $validatedData);

        // Combinaison du tableau en une seule chaîne
        $verificationCode = implode('', $validatedData['verification_code']);
        Log::info('Code OTP combiné', ['code' => $verificationCode]);

        // Récupération des informations Twilio
        // $sid = env('TWILIO_SID');
        // $token = env('TWILIO_TOKEN');
        // $verifyServiceId = env('TWILIO_VERIFY_SERVICE_ID');
        // $twilio = new Client($sid, $token);

        try {
            // Vérification du code via Twilio Verify
            Log::info('Envoi du code OTP à Twilio pour vérification.', ['phone' => $validatedData['phone'], 'code' => $verificationCode]);

            // $verificationCheck = $twilio->verify->v2->services($verifyServiceId)
            //     ->verificationChecks
            //     ->create([
            //         'to' => $validatedData['phone'],
            //         'code' => $verificationCode // Utilisation du code combiné
            //     ]);


            // Log::info('Réponse de Twilio', ['status' => $verificationCheck->status]);

            // if ($verificationCheck->status == 'approved') {
            Log::info('Code OTP approuvé pour le téléphone', ['phone' => $validatedData['phone']]);

            // Récupération de l'utilisateur lié au numéro de téléphone
            $user = User::where('phone', $validatedData['phone'])->firstOrFail();
            Log::info('Utilisateur trouvé', ['user_id' => $user->id]);

            // Création de l'investisseur
            $investisseur = new Investisseur();
            $investisseur->nom = $user->name;
            $investisseur->prenom = $user->username;
            $investisseur->tranche = $user->investissement;
            $investisseur->invest_type = $user->invest_type;
            $investisseur->user_id = $user->id;
            $investisseur->save();
            Log::info('Investisseur créé avec succès', ['investisseur_id' => $investisseur->id]);

            // Création des sous-comptes
            $this->createUserWallets($user->id);
            Log::info('Sous-comptes créés pour l\'utilisateur', ['user_id' => $user->id]);

            return redirect()->route('biicf.login')
                ->with('success', 'Votre numéro a été vérifié avec succès et vous avez été ajouté en tant qu\'investisseur !');
            // } else {
            //     Log::warning('Code OTP incorrect pour le téléphone', ['phone' => $validatedData['phone']]);
            //     return back()->withErrors(['verification_code' => 'Code de vérification incorrect.']);
            // }
        } catch (\Exception $e) {
            Log::error('Erreur lors de la vérification du code OTP', ['message' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la vérification du code.']);
        }
    }


    public function showPhoneVerificationForm()
    {
        return view('auth.verify-phone');
    }



    private function createUserWallets($userId)
    {
        // Création d'un portefeuille principal
        $wallet = new Wallet();
        $wallet->user_id = $userId;
        $wallet->balance = 0; // Solde initial
        $wallet->Numero_compte = $this->generateIban($userId); // Ajout d'un numéro de compte
        $wallet->save();

        // Création des sous-comptes associés
        $this->createWallet($wallet->id, new Coi(), 'type_compte', 'COI');
        $this->createWallet($wallet->id, new Cfa(), 'type_compte', 'CFA');
        $this->createWallet($wallet->id, new Cefp(), 'type_compte', 'CEFP');
        $this->createWallet($wallet->id, new Cedd(), 'type_compte', 'CEDD');
        $this->createWallet($wallet->id, new Crp(), 'type_compte', 'CRP');
    }

    private function createWallet($walletId, $walletInstance, $field, $value)
    {
        $walletInstance->id_wallet = $walletId;
        $walletInstance->Date_Creation = now();
        $walletInstance->$field = $value; // Assigner la valeur au champ spécifié
        $walletInstance->Solde = 0; // Solde initial
        $walletInstance->save();
    }

    private function generateIban($userId)
    {
        $countryCode = 'CI'; // Exemple : Code de la Côte d'Ivoire

        do {
            // Générer un numéro de compte basé sur l'ID utilisateur et un composant aléatoire pour garantir l'unicité
            $randomComponent = rand(1000, 9999); // Générer une partie aléatoire
            $accountNumber = str_pad($userId, 12, '0', STR_PAD_LEFT) . $randomComponent;  // Numéro de compte unique
            $iban = $countryCode . $accountNumber;

            // Vérifier si l'IBAN existe déjà dans la base de données
            $existingWallet = Wallet::where('Numero_compte', $iban)->first();
        } while ($existingWallet); // Recommencer jusqu'à ce que l'IBAN soit unique

        return $iban;
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

    public function detailprojetGroupe($id)
    {
        return view('finance.detailprojet', ['id' => $id, 'id_projet' => null]);
    }
    public function detailprojetNegocie($id)
    {
        return view('finance.detailprojet', ['id' => null, 'id_projet' => $id]);
    }


    public function detailcredit($id)
    {
        return view('finance.detailcredit', ['id' => $id, 'id_projet' => null]); // Si vous n'avez pas d'ID de projet
    }

    public function detailcreditprojet($id)
    {
        return view('finance.detailcredit', ['id' => null, 'id_projet' => $id]); // Passer un ID de projet
    }
}

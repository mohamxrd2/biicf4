<?php

namespace App\Http\Controllers;

use App\Models\Cfa;
use App\Models\Coi;
use App\Models\Crp;
use App\Models\Cedd;
use App\Models\Cefp;
use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use Twilio\Rest\Client;
use App\Models\Transaction;
use App\Models\Consommation;
use App\Models\Investisseur;
use Illuminate\Http\Request;
use App\Models\ProduitService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

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


    public function resetPassword($username)
    {
        // Trouver l'utilisateur
        $user = User::where('username', $username)->first();

        if (!$user) {
            return redirect()->back()->with('error', 'Utilisateur non trouvé.');
        }

        // Changer son mot de passe
        $user->password = Hash::make('Azerty12345');
        $user->save();

        return redirect()->back()->with('success', 'Mot de passe réinitialisé avec succès.');
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
        // $sid = config('services.twilio.sid');
        // $token = config('services.twilio.token');
        // $twilioPhoneNumber  = config('services.twilio.verify_service_id');

        // dd($sid, $token, $twilioPhoneNumber );

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

        try {
            // // Tentative d'envoi de SMS avant la création de l'utilisateur
            // try {
            //     // Envoi du SMS de vérification
            //     $this->sendSmsVerification($validatedData['phone']);

            //     // Si l'envoi du SMS réussit, on stocke le code OTP et l'état dans la session
            //     session(['phone' => $validatedData['phone'], 'otp_sent_at' => now()]);
            // } catch (Exception $smsException) {
            //     // Si l'envoi du SMS échoue, on retourne une erreur
            //     return back()->withErrors(['sms_error' => 'Impossible d\'envoyer le SMS de vérification. Veuillez réessayer plus tard.'])->withInput();
            // }

            // Si l'envoi du SMS a réussi, on procède à la création de l'utilisateur
            $user = new User();
            $user->name = $validatedData['name'] . ' ' . $request->input('last-name');
            $user->username = $validatedData['username'];
            $user->email = $validatedData['email'];
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
            // $user->save();


            ////pour les test sans sms


            // Marquer le numéro comme vérifié
            $user->email_verified_at = now();
            $user->save();

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
            //////////////////////

            // Redirection vers la page de vérification du téléphone
            // return redirect()->route('verify.phone', [
            //     'phone' => Crypt::encryptString($user->phone)
            // ])->with('success', 'Code de vérification envoyé par SMS. Veuillez vérifier votre numéro.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator->errors())->withInput();
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    /**
     * Envoie un code de vérification par SMS via Twilio
     *
     * @param string $phoneNumber Numéro de téléphone du destinataire
     * @return void
     * @throws Exception Si l'envoi du SMS échoue
     */
    private function sendSmsVerification($phoneNumber)
    {
        try {
            // Génération d'un code OTP à 6 chiffres
            $otp = sprintf("%06d", mt_rand(1, 999999));

            // Stockage du code OTP dans la session
            session(['verification_code' => $otp]);

            // Récupération des informations Twilio depuis le fichier .env
            $sid = config('services.twilio.sid');
            $token = config('services.twilio.token');
            $twilioPhoneNumber = config('services.twilio.phone_number');

            // Vérification des variables
            if (!$sid || !$token || !$twilioPhoneNumber) {
                Log::error('Twilio: Identifiants manquants');
                throw new Exception('Configuration Twilio manquante.');
            }

            // Formatage du numéro de téléphone au format E.164 si nécessaire
            if (!Str::startsWith($phoneNumber, '+')) {
                // Ajoutez le code du pays approprié (exemple avec +225 pour la Côte d'Ivoire)
                $phoneNumber = '+' . ltrim($phoneNumber, '0');
            }

            $twilio = new Client($sid, $token);

            // Envoi direct du SMS avec le code OTP
            $message = $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => "Votre code de vérification BIICF est : $otp. Ce code expire dans 10 minutes."
                ]
            );

            // Enregistrement du moment d'envoi et du code pour gérer l'expiration
            session(['otp_sent_at' => now(), 'verification_code' => $otp]);

            Log::info("SMS envoyé au numéro $phoneNumber avec le SID: " . $message->sid);
        } catch (Exception $e) {
            Log::error('Erreur lors de l\'envoi du SMS: ' . $e->getMessage());
            throw new Exception('Échec de l\'envoi du SMS: ' . $e->getMessage());
        }
    }


    public function showPhoneVerificationForm(Request $request)
    {
        try {
            $encryptedPhone = $request->query('phone'); // Récupérer le numéro chiffré depuis l'URL
            $phone = Crypt::decryptString($encryptedPhone); // Déchiffrer le numéro

            return view('auth.verify-phone', ['phone' => $phone]);
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Numéro de téléphone invalide ou corrompu.']);
        }
    }

    /**
     * Vérifie le code OTP soumis par l'utilisateur
     *
     * @param Request $request La requête contenant le code OTP et le numéro de téléphone
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyPhoneCode(Request $request)
    {
        // Validation du code de vérification
        $validatedData = $request->validate([
            'verification_code' => 'required|array|min:6|max:6',
            'verification_code.*' => 'required|string|max:1',
            'phone' => 'required|string'
        ]);

        Log::info('Début de la vérification du code OTP.', ['phone' => $request->phone]);

        // Récupérer l'OTP stocké en session et le timestamp d'envoi
        $storedCode = session('verification_code');
        $otpSentAt = session('otp_sent_at');

        // Vérifier si l'OTP est encore valide (expiré après 10 minutes)
        if (!$storedCode || !$otpSentAt || now()->diffInMinutes($otpSentAt) > 10) {
            return back()->withErrors(['verification_code' => 'Le code a expiré. Veuillez demander un nouveau code.']);
        }

        // Combinaison du tableau en une seule chaîne
        $submittedCode = implode('', $validatedData['verification_code']);
        Log::info('Code OTP soumis', ['code' => $submittedCode, 'stored_code' => $storedCode]);

        // Vérification directe du code OTP (sans Twilio Verify)
        if ($submittedCode === $storedCode) {
            Log::info('Code OTP validé pour le téléphone', ['phone' => $validatedData['phone']]);

            // Récupération de l'utilisateur
            try {
                $user = User::where('phone', $validatedData['phone'])->firstOrFail();
                Log::info('Utilisateur trouvé', ['user_id' => $user->id]);

                if ($user->email_verified_at) {
                    Log::warning('Numéro déjà vérifié.', ['phone' => $validatedData['phone']]);
                    return back()->withErrors([
                        'verification_code' => 'Ce numéro a déjà été vérifié.'
                    ]);
                }

                // Marquer le numéro comme vérifié
                $user->email_verified_at = now();
                $user->save();

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

                // Nettoyer les variables de session
                session()->forget(['verification_code', 'otp_sent_at']);

                return redirect()->route('biicf.login')
                    ->with('success', 'Votre numéro a été vérifié avec succès et vous avez été ajouté en tant qu\'investisseur !');
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                Log::error('Utilisateur non trouvé avec ce numéro', ['phone' => $validatedData['phone']]);
                return back()->withErrors(['error' => 'Aucun utilisateur trouvé avec ce numéro de téléphone.']);
            }
        } else {
            Log::warning('Code OTP incorrect pour le téléphone', ['phone' => $validatedData['phone']]);
            return back()->withErrors(['verification_code' => 'Code de vérification incorrect.']);
        }
    }

    public function resendOtp(Request $request)
    {
        // Récupérer le numéro de téléphone depuis la requête
        $phone = $request->query('phone');

        // Vérifier si le numéro de téléphone est valide
        if (!$phone) {
            return back()->withErrors(['error' => 'Numéro de téléphone invalide.']);
        }

        // Vérification de l'OTP
        $otpSentAt = session('otp_sent_at');

        // Vérifier si l'OTP a expiré (par exemple après 10 minutes)
        if ($otpSentAt && now()->diffInMinutes($otpSentAt) <= 10) {
            // Si l'OTP est encore valide, informer l'utilisateur
            return back()->withErrors(['error' => 'Vous devez attendre 10min avant de renvoyer un autre code.']);
        }

        // Sinon, envoyer un nouveau code OTP
        $this->sendSmsVerification($phone);

        // Mettre à jour la session avec la nouvelle date d'envoi de l'OTP
        session(['otp_sent_at' => now()]);

        // Rediriger avec un message de succès
        return back()->with('success', 'Un nouveau code OTP a été envoyé.');
    }

    private function createUserWallets($userId)
    {
        // Création d'un portefeuille principal
        $wallet = new Wallet();
        $wallet->user_id = $userId;
        $wallet->balance = 0; // Solde initial
        $wallet->Numero_compte = $this->generateUniqueAccountNumber(); // Ajout d'un numéro de compte
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

    function generateUniqueAccountNumber()
    {
        do {
            // Génère un numéro aléatoire de 12 chiffres
            $accountNumber = mt_rand(100000000000, 999999999999);

            // Vérifie s'il existe déjà dans la base de données
            $exists = DB::table('wallets')->where('Numero_compte', $accountNumber)->exists();
        } while ($exists);

        return $accountNumber;
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

    public function detaildeposit($id)
    {
        return view('admin.detail-deposit', compact('id'));
    }
    public function detailretrait($id)
    {
        return view('admin.detail-retrait', compact('id'));
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
        return view('finance.detailcredit', ['id' => $id, 'id_projet' => null, 'id_details' => null]); // Si vous n'avez pas d'ID de projet
    }

    public function detailcreditprojet($id)
    {
        return view('finance.detailcredit', ['id' => null, 'id_projet' => $id, 'id_details' => null]); // Passer un ID de projet
    }
    public function gagnantNegocation($id)
    {
        return view('finance.detailcredit', ['id' => null, 'id_projet' => null, 'id_details' => $id]); // Passer un ID de projet
    }
}

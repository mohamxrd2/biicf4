<?php

use App\Livewire\Cfa;
use App\Livewire\Notif;
use App\Livewire\Accueil;
use App\Livewire\Tontine;
use App\Jobs\TestQueueJob;
use App\Livewire\Notification;
use App\Livewire\ProduitOffre;
use App\Livewire\Walletclient;
use App\Livewire\Consommations;
use App\Livewire\ProjetDetails;
use App\Livewire\RechargeAgent;
use App\Livewire\Remboursement;

use App\Livewire\ProduitService;
use App\Livewire\DetailLivraison;
use App\Livewire\NotificationShow;
use App\Livewire\PostulerComponent;
use App\Http\Controllers\OffreNegos;
use App\Livewire\AjoutConsommations;
use App\Livewire\NotificationDetail;
use Illuminate\Support\Facades\Auth;
use App\Livewire\DetailsCreditProjet;
use App\Livewire\WithdrawalComponent;
use Illuminate\Support\Facades\Route;
use App\Livewire\AjoutProduitServices;
use App\Http\Controllers\SmsController;
use App\Livewire\ProduitServiceDetails;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsoController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\AchatGroupController;
use App\Http\Controllers\AdminAgentController;
use App\Http\Controllers\AdminChartController;
use App\Http\Controllers\AppelOffreController;
use App\Http\Controllers\AchatDirectController;
use App\Http\Controllers\AdminWalletController;
use App\Http\Middleware\CacheControlMiddleware;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OffreClientControllerr;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\BiicfAuthController;
use App\Http\Controllers\ProduitServiceController;
use App\Http\Controllers\OffreGroupClientController;
use App\Livewire\DetailTontine;
use App\Livewire\Profile;

Route::get('/', function () {
    return view('index');
})->name('index');


Route::prefix('admin')->middleware('admin.auth')->group(function () {
    //liste des dashboard
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/statistique', [AdminChartController::class, 'index'])->name('admin.statistique');
    //route de gestion du wallet
    Route::get('/porte-feuille', [AdminWalletController::class, 'index'])->name('admin.porte-feuille');


    //liste des agents
    Route::get('/agent', [AdminAgentController::class, 'index'])->name('admin.agent');
    //liste des clients
    Route::get('/client', [UserController::class, 'listUserAdmin'])->name('admin.client');

    //publication des produits et services
    Route::get('/produits', [ProduitServiceController::class, 'adminProduct'])->name('admin.produits');
    Route::get('/services', [ProduitServiceController::class, 'adminService'])->name('admin.services');


    //liste des consommation produits et services
    Route::get('/consommation-produit', [ConsoController::class, 'adminConsProd'])->name('admin.conso-produit');
    Route::get('/consommation-service', [ConsoController::class, 'adminConsServ'])->name('admin.conso-service');

    //consommation show
    Route::get('/consommation/{id}', [UserController::class, 'consoShow'])->name('consommation.consoShow');

    //consommation show
    Route::post('/consommation/{id}', [UserController::class, 'consoEtat'])->name('consommation.consoEtat');

    Route::get('/profile', function () {
        return view('admin.profile');
    })->name('admin.profile');

    Route::get('/reglage', [AdminSettingController::class, 'index'])->name('admin.reglage');

    Route::post('/client/storePub', [UserController::class, 'storePub'])->name('admin.client.storePub');
    Route::post('/client/storeCons', [UserController::class, 'storeCons'])->name('admin.client.storeCons');


    Route::post('/agent/{admin}', [AdminAgentController::class, 'isban'])->name('admin.agent.isban');

    Route::delete('/users/{user}', [UserController::class, 'destroyUser'])->name('admin.user.destroy');

    Route::delete('/produit/{produit}', [ProduitServiceController::class, 'destroyProduct'])->name('admin.products.destroy');

    Route::delete('/services/{services}', [ProduitServiceController::class, 'destroyService'])->name('admin.services.destroy');



    Route::put('/profile/update/{admin}', [AdminsController::class, 'updateProfile'])->name('admin.updateProfile');
    Route::put('/profile/password/{admin}', [AdminsController::class, 'updatePassword'])->name('admin.updatePassword');

    Route::put('/profile/profile-photo/{admin}', [AdminsController::class, 'updateProfilePhoto'])->name('admin.updateProfilePhoto');

    //agent show
    Route::get('/agent/{username}', [AdminAgentController::class, 'show'])->name('agent.show');

    Route::get('/client/{username}', [UserController::class, 'show'])->name('client.show');

    Route::get('/produit/{id}', [UserController::class, 'pubShow'])->name('produit.pubShow');

    Route::post('/produit/{id}', [UserController::class, 'etat'])->name('produit.etat');


    Route::get('/resetPassword/{username}', [UserController::class, 'resetPassword'])->name('client.resetPassword');
    Route::get('/edit-agent/{username}', [UserController::class, 'editAgent'])->name('client.editad');
    Route::post('/edit-agent/{username}', [UserController::class, 'updateAdmin'])->name('update.admin');


    //success
    Route::get('/ajouter-client', [UserController::class, 'createPageAdmin'])->name('clients.create');
    Route::post('/ajouter-client', [UserController::class, 'createUserAdmin'])->name('clients.store');


    Route::get('/demande', [UserController::class, 'livraisonliste'])->name('admin.demande');
    Route::get('demande-livraison/{id}', [UserController::class, 'detaillivraison'])->name('livraison.show');
    Route::get('demande-psap{id}', [UserController::class, 'detailpsap'])->name('psap.show');
    Route::get('demande-deposits/{id}', [UserController::class, 'detaildeposit'])->name('deposits.show');
    Route::get('demande-retrait/{id}', [UserController::class, 'detailretrait'])->name('retrait.show');

    Route::get('projet', function () {
        return view('admin.projetlist');
    })->name('admin.projetlist');

    Route::get('/projet/{id}', [AdminsController::class, 'detailprojet'])->name('projet.details');
});

//email
Route::get('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify');

Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


//////   ////// PLATEFORME //////   ///////////

Route::middleware(['user.auth', CacheControlMiddleware::class])
    ->prefix('biicf')
    ->group(function () {
        Route::get('acceuil', Accueil::class)->name('biicf.acceuil')->middleware('cache.headers:public;max_age=31536000;immutable');
        Route::get('notification', Notification::class)->name('biicf.notif');
        Route::get('notification/detail/{id}', NotificationDetail::class)->name('notification.show');

        //TEST
        Route::get('test-queue', function () {
            TestQueueJob::dispatch();
            return 'Job dispatché !';
        });
        Route::get('/test-error/{code}', function ($code) {
            abort($code);
        });


        Route::get('porte-feuille', Walletclient::class)->name('biicf.wallet');
        Route::get('porte-feuille/remboursement', Remboursement::class)->name('biicf.remboursement');

        Route::get('porte-feuille/tontine', Tontine::class)->name('tontine');
        Route::get('porte-feuille/tontine/details/{id}', DetailTontine::class)->name('tontine.detail');



        Route::get('publication', ProduitService::class)->name('biicf.post');
        Route::get('publication/creer-produit', AjoutProduitServices::class)->name('biicf.postProduit');

        Route::get('publication/{id}', ProduitServiceDetails::class)->name('biicf.postdet');
        Route::get('publication/offre/{id}', ProduitOffre::class)->name('biicf.offre');


        Route::get('consommation', Consommations::class)->name('biicf.conso');
        Route::get('consommation/creer-consommation', AjoutConsommations::class)->name('biicf.postCons');
        Route::get('consommation/{id}', [ConsoController::class, 'consoDet'])->name('biicf.consoDet');



        Route::get('retrait', [AdminWalletController::class, 'retrait'])->name('biicf.retrait');

        Route::post('envoyer-client', [AdminWalletController::class, 'sendToClientAccount'])->name('biicf.send');

        Route::get('profile', Profile::class)->name('biicf.profile');

        Route::get('Appel-offre', [AppelOffreController::class, 'search'])->name('biicf.appeloffre');
        Route::match(['get', 'post'], 'formumelaire-appel-offre', [AppelOffreController::class, 'formAppel'])->name('biicf.form');
        Route::post('formumelaire-appel-offre/store', [AppelOffreController::class, 'storeAppel'])->name('biicf.formstore');
        Route::post('formumelaire-appel-offre/comment', [AppelOffreController::class, 'comment'])->name('biicf.comment');
        Route::post('formumelaire-appel-offregroupe/store', [AppelOffreController::class, 'formstoreGroupe'])->name('biicf.formstoreGroupe');


        Route::get('postuler', PostulerComponent::class)->name('biicf.postuler');


        //Route pour bourse du financement

        Route::get('finance/acceuil', function () {
            return view('finance.acceuil');
        })->name('finance.acceuil');

        Route::get('finance/recherche', function () {
            return view('finance.search');
        })->name('finance.search');

        Route::get('finance/notification', function () {
            return view('finance.notif');
        })->name('finance.notif');

        Route::match(['get', 'post'], 'finance/Porte-feuille', function () {
            return view('finance.wallet');
        })->name('finance.wallet');

        Route::get('finance/projet', function () {
            return view('finance.projet');
        })->name('finance.projet');

        Route::get('finance/profile', function () {
            return view('finance.profile');
        })->name('finance.profile');

        Route::get('finance/Ajouter-projet', function () {
            return view('finance.addproject');
        })->name('finance.addproject');

        Route::get('finance/detail-projet/{id}', [UserController::class, 'detailprojetGroupe'])->name('detailprojetGroupe');
        Route::get('finance/detail-projet-negocie/{id}', [UserController::class, 'detailprojetNegocie'])->name('detailprojetNegocie');
        Route::get('finance/detail-credit/{id}', [UserController::class, 'detailcredit'])->name('detailcredit');
        Route::get('finance/detail-credit-projet/{id}', [UserController::class, 'detailcreditprojet'])->name('detailcreditprojet');
        Route::get('finance/gagnant-negocation/{id}', [UserController::class, 'gagnantNegocation'])->name('gagnantNegocation');
    });


Route::get('biicf/login', [BiicfAuthController::class, 'showLoginForm'])->name('biicf.login');
Route::post('biicf/login', [BiicfAuthController::class, 'login']);

Route::get('biicf/signup', [UserController::class, 'createPageBiicf'])->name('biicf.signup');
Route::post('biicf/signup', [UserController::class, 'createUserBiicf']);

Route::get('biicf/verify-phone', [UserController::class, 'showPhoneVerificationForm'])->name('verify.phone');
Route::post('biicf/verify-phone', [UserController::class, 'verifyPhoneCode'])->name('verify.phone.code');
Route::get('resend-otp', [UserController::class, 'resendOtp'])->name('resend.otp');


Route::post('biicf/logout', [BiicfAuthController::class, 'logout'])
    ->name('biicf.logout')
    ->middleware(['web', 'auth']);

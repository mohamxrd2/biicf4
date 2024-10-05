<?php

use App\Livewire\ProjetDetails;
use App\Livewire\RechargeAgent;
use App\Livewire\DetailLivraison;
use App\Http\Controllers\OffreNegos;

use App\Livewire\WithdrawalComponent;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SmsController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ConsoController;
use App\Http\Controllers\AdminsController;
use App\Http\Controllers\AchatGroupController;
use App\Http\Controllers\AdminAgentController;
use App\Http\Controllers\AdminChartController;
use App\Http\Controllers\AppelOffreController;
use App\Http\Controllers\AchatDirectController;
use App\Http\Controllers\AdminWalletController;
use App\Http\Controllers\AdminSettingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OffreClientControllerr;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\Auth\AdminAuthController;
use App\Http\Controllers\Auth\BiicfAuthController;
use App\Http\Controllers\ProduitServiceController;
use App\Http\Controllers\OffreGroupClientController;

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





    Route::get('/edit-agent/{username}', [UserController::class, 'editAgent'])->name('client.editad');
    Route::post('/edit-agent/{username}', [UserController::class, 'updateAdmin'])->name('update.admin');


    //success
    Route::get('/ajouter-client', [UserController::class, 'createPageAdmin'])->name('clients.create');
    Route::post('/ajouter-client', [UserController::class, 'createUserAdmin'])->name('clients.store');


    Route::get('/demande', [UserController::class, 'livraisonliste'])->name('admin.demande');
    Route::get('demande-livraison/{id}', [UserController::class, 'detaillivraison'])->name('livraison.show');
    Route::get('demande-psap{id}', [UserController::class, 'detailpsap'])->name('psap.show');

    Route::get('projet', function(){
        return view ('admin.projetlist');
    })->name('admin.projetlist');

    Route::get('/projet/{id}',[AdminsController::class, 'detailprojet'] )->name('projet.details');




});

//email
Route::get('/email/verify', [VerificationController::class, 'verify'])->name('verification.verify');

Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login']);
Route::post('admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');


//////   ////// PLATEFORME //////   ///////////

Route::middleware('user.auth')->prefix('biicf')->group(function () {
    Route::get('acceuil', [ProduitServiceController::class,  'homeBiicf'])->name('biicf.acceuil');
    Route::get('recheche', [ProduitServiceController::class, 'search'])->name('biicf.search');



    Route::get('notif', [NotificationController::class, 'index'])->name('biicf.notif');

    Route::get('api/notifications', [NotificationController::class, 'getNotifications'])->name('api.notifications');

    //accepter ou refuser la cmmande
    Route::post('notification/accepter', [AchatDirectController::class, 'accepter'])->name('achatD.accepter');
    Route::post('notification/refuser', [AchatDirectController::class, 'refuser'])->name('achatD.refuser');
    Route::get('/notification/{id}', [NotificationController::class, 'show'])->name('notification.show');


    // routes/web.php
    Route::post('notification/acceptergroupe', [AchatGroupController::class, 'accepter'])->name('achatG.accepter');
    Route::post('notification/refusergroupe', [AchatGroupController::class, 'refuser'])->name('achatG.refuser');
    Route::get('publication', [ProduitServiceController::class, 'postBiicf'])->name('biicf.post');
    Route::post('publication/ajouter', [UserController::class, 'storePub'])->name('biicf.pubstore');
    Route::delete('publication/supprimer/{produit}', [ProduitServiceController::class, 'destroyProductBiicf'])->name('biicf.pubdeleteBiicf');
    //la vue du formulaire
    Route::get('publication/creer-produit', [ProduitServiceController::class, 'postProduit'])->name('biicf.postProduit');
    Route::get('publication/{id}', [ProduitServiceController::class, 'pubDet'])->name('biicf.postdet');


    Route::get('consommation/creer-consommation', [ConsoController::class, 'postCons'])->name('biicf.postCons');
    Route::get('consommation', [ConsoController::class, 'consoBiicf'])->name('biicf.conso');
    Route::post('consommation/ajouter', [UserController::class, 'storeCons'])->name('biicf.storeCons');
    Route::delete('consommation/supprimer/{conso}', [ConsoController::class, 'destroConsom'])->name('biicf.consodelete');
    Route::get('consommation/{id}', [ConsoController::class, 'consoDet'])->name('biicf.consoDet');



    Route::get('porte-feuille', [AdminWalletController::class, 'indexBiicf'])->name('biicf.wallet');
    Route::get('retrait', [AdminWalletController::class, 'retrait'] )->name('biicf.retrait');


    Route::post('envoyer-client', [AdminWalletController::class, 'sendToClientAccount'])->name('biicf.send');




    Route::get('profile', [UserController::class, 'showProfile'])->name('biicf.showProfile');
    Route::put('/profile/profile-photo/{user}', [UserController::class, 'updateProfilePhoto'])->name('biicf.updateProfilePhoto');
    Route::put('/profile/update/{user}', [UserController::class, 'updateProfile'])->name('biicf.updateProfile');
    Route::put('/profile/password/{user}', [UserController::class, 'updatePassword'])->name('biicf.updatePassword');

    Route::get('Appel-offre', [AppelOffreController::class, 'search'])->name('biicf.appeloffre');
    Route::match(['get', 'post'], 'formumelaire-appel-offre', [AppelOffreController::class, 'formAppel'])->name('biicf.form');
    Route::post('formumelaire-appel-offre/store', [AppelOffreController::class, 'storeAppel'])->name('biicf.formstore');
    Route::post('formumelaire-appel-offre/comment', [AppelOffreController::class, 'comment'])->name('biicf.comment');
    Route::post('formumelaire-appel-offregroupe/store', [AppelOffreController::class, 'formstoreGroupe'])->name('biicf.formstoreGroupe');

    // Route pour afficher les détails de l'offre et ajouter des quantités
    Route::get('formumelaire-appel-offregroupe/{id}', [AppelOffreController::class, 'detailoffre'])->name('biicf.detailoffre');

    // Route pour stocker l'ajout de quantités à l'offre
    Route::post('formumelaire-appel-offregroupe/storeoffre', [AppelOffreController::class, 'storeoffre'])->name('biicf.storeoffre');

    Route::post('offreClient/store', [OffreClientControllerr::class, 'sendoffre'])->name('biicf.sendoffre');

    Route::post('offregroupClient/store', [OffreGroupClientController::class, 'sendoffGrp'])->name('biicf.sendoffregrp');
    Route::post('offregroupClient/comment', [OffreGroupClientController::class, 'commentoffgroup'])->name('biicf.offgrpcomment');

    Route::post('offreneg/store', [OffreNegos::class, 'store'])->name('biicf.sendoffreneg');
    Route::post('offreneg/ajouter', [OffreNegos::class, 'add'])->name('biicf.addquantity');
    Route::post('offreneg/accepeter', [OffreNegos::class, 'accepter'])->name('biicf.offNAccept');

    Route::post('offrenegroup', [OffreNegos::class, 'offregroupneg'])->name('biicf.offregroupneg');

    Route::get('postuler', function(){
        return view ('biicf.postuler');
    })->name('biicf.postuler');


    //Route pour bourse du financement

    Route::get('finance/acceuil', function(){
        return view ('finance.acceuil');
    })->name('finance.acceuil');

    Route::get('finance/recherche', function(){
        return view ('finance.search');
    })->name('finance.search');

    Route::get('finance/notification', function(){
        return view ('finance.notif');
    })->name('finance.notif');

    Route::get('finance/Porte-feuille', function(){
        return view ('finance.wallet');
    })->name('finance.wallet');

    Route::get('finance/projet', function(){
        return view ('finance.projet');
    })->name('finance.projet');

    Route::get('finance/profile', function(){
        return view ('finance.profile');
    })->name('finance.profile');

    Route::get('finance/Ajouter-projet', function(){
        return view ('finance.addproject');
    })->name('finance.addproject');

    Route::get('finance/detail-projet/{id}', [UserController::class, 'detailprojet'])->name('detailprojet');



});





Route::get('biicf/login', [BiicfAuthController::class, 'showLoginForm'])->name('biicf.login');
Route::post('biicf/login', [BiicfAuthController::class, 'login']);

Route::get('biicf/signup', [UserController::class, 'createPageBiicf'])->name('biicf.signup');
Route::post('biicf/signup', [UserController::class, 'createUserBiicf']);

Route::get('biicf/verify-phone', [UserController::class, 'showPhoneVerificationForm'])->name('verify.phone');
Route::post('biicf/verify-phone', [UserController::class, 'verifyPhoneCode'])->name('verify.phone.code');

Route::post('biicf/logout', [BiicfAuthController::class, 'logout'])->name('biicf.logout');

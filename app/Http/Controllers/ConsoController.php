<?php

namespace App\Http\Controllers;

use App\Models\Consommation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;

class ConsoController extends Controller
{
    public function adminConsProd()
    {
        return view('admin.conso-produit');
    }

    public function adminConsServ(){

        return view('admin.conso-service');

    }

    public function consoBiicf()
    {
        // Récupérer l'utilisateur connecté via le gardien web

            // Récupérer les consommations associés à cet utilisateur

            $user = Auth::guard('web')->user();
            $consommations = Consommation::where('id_user', $user->id)->orderBy('created_at', 'desc')
                ->paginate(10);


            // Compter le nombre de consommations
            $consoCount = $consommations->count();

            // Passer les consommations à la vue
            return view('biicf.conso', ['consommations' => $consommations, 'consoCount' => $consoCount]);


    }

    public function destroConsom($id)
    {
        $consommation = Consommation::find($id);

        if (!$consommation) {
            return redirect()->back()->with('error', 'consommation non trouvé.');
        }

        $consommation->delete();

        return redirect()->back()->with('success', 'consommation supprimé avec succès.');
    }

    public function consoDet($id)
    {
        $consommation = Consommation::find($id);

        return view('biicf.consodetail', compact('consommation'));
    }
    public function postCons()
    {

        return view('biicf.AjoutConsommation');
    }
}

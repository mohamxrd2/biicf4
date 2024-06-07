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
        $consommations = Consommation::where('type', 'produits')->orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.conso-produit', ['consommations' => $consommations]);

    }

    public function destroyConsprod($id){
        $consommation = Consommation::find($id);

        if(!$consommation){
            return redirect()->back()->with('error', 'Consommation non trouvée.');
        }

        $consommation->delete();

        return back()->with('success', 'La consommation a été supprimée avec succès');
    }
    public function adminConsServ(){
        $consommations = Consommation::where('type', 'services')->orderBy('created_at', 'DESC')->paginate(10);

        return view('admin.conso-service', ['consommations' => $consommations]);
    }
    public function destroyConsserv($id){
        $consommation = Consommation::find($id);

        if(!$consommation){
            return redirect()->back()->with('error', 'Consommation non trouvée.');
        }

        $consommation->delete();

        return back()->with('success', 'La consommation a été supprimée avec succès');
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
}

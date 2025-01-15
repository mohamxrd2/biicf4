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

    public function adminConsServ()
    {

        return view('admin.conso-service');
    }

    public function consoBiicf()
    {
        // Récupérer l'utilisateur connecté via le gardien web
        $user = Auth::guard('web')->user();

        // Récupérer les consommations associées à cet utilisateur
        $consommations = Consommation::where('id_user', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Compter le nombre de consommations
        $consoCount = $consommations->count();

        // Passer les consommations à la vue
        return view('biicf.conso', [
            'consommations' => $consommations,
            'consoCount' => $consoCount
        ]);
    }




    public function consoDet($id)
    {
        $consommations = Consommation::find($id);

        return view('biicf.consodetail', compact('consommations'));
    }
  
}

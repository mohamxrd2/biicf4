<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProduitService;

class AppelOffre extends Controller
{
    //

    public function search(Request $request) {
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
    
        $results = $produits->get();
        $resultCount = $results->count();
        $prodUsers = $results->pluck('user.id')->unique('id');

        $prodUsersCount = $results->pluck('user')->unique('id')->count();

        $produitDims = ProduitService::with('user')
        ->where('statuts', 'Accepté')
        ->orderBy('created_at', 'desc');
    
        return view('biicf.searchAppelOffre', compact('results', 'resultCount', 'keyword', 'prodUsers', 'produitDims', 'prodUsersCount'));
    }
    
}

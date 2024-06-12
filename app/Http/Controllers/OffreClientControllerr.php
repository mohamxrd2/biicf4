<?php

namespace App\Http\Controllers;

use App\Models\Consommation;
use App\Models\ProduitService;
use App\Models\User;
use App\Notifications\OffreNotif;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class OffreClientControllerr extends Controller
{
    public function sendoffre(Request $request)
    {
        // Récupérer l'ID du produit à partir du formulaire
        $produitId = $request->input('produit_id');

        // Trouver le produit ou échouer
        $produit = ProduitService::findOrFail($produitId);
        $nomProduit = $produit->name;

        // Requête pour récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $nomProduit)
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Envoyer une notification à chaque propriétaire
        foreach ($idsProprietaires as $userId) {
            // Assurez-vous que le modèle User a été importé
            $user = User::find($userId);
            if ($user) {
                // Envoyer une notification (il faut créer une notification pour cela)
                Notification::send($user, new OffreNotif($produit));
            }
        }

        // Retourner une réponse ou rediriger avec un message de succès
        return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
    }
}

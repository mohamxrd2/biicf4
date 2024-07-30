<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consommation;
use App\Notifications\OffreNotifGroup;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;

class OffreGroupClientController extends Controller
{
    public function sendoffGrp(Request $request)
    {
        $user_id = Auth::guard('web')->id();

        // Récupérer l'ID du produit à partir du formulaire
        $produitId = $request->input('produit_id');

        // Trouver le produit ou échouer
        $produit = ProduitService::findOrFail($produitId);
        $nomProduit = $produit->name;

        // Générer un code unique une seule fois pour tous les utilisateurs
        $Uniquecode = $this->genererCodeAleatoire(10);

        // Requête pour récupérer les IDs des propriétaires des consommations similaires
        $idsProprietaires = Consommation::where('name', $nomProduit)
            ->where('id_user', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();

        // Envoyer une notification à chaque propriétaire
        foreach ($idsProprietaires as $userId) {
            // Assurez-vous que le modèle User a été importé
            $user = User::find($userId);
            if ($user) {
                // Envoyer une notification avec le code unique
                Notification::send($user, new OffreNotifGroup($produit, $Uniquecode));
            }
        }

        // Retourner une réponse ou rediriger avec un message de succès
        return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
    }

    private function genererCodeAleatoire($longueur)
    {
        $caracteres = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        $code = '';

        for ($i = 0; $i < $longueur; $i++) {
            $code .= $caracteres[rand(0, strlen($caracteres) - 1)];
        }

        return $code;
    }


}

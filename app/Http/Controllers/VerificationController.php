<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class VerificationController extends Controller
{
    public function verify(Request $request)
    {
        // Extraire les paramètres de la requête
        $userId = $request->get('id');
        $verificationToken = $request->get('token');

        // Rechercher l'utilisateur correspondant dans la base de données
        $user = User::find($userId);

        // Vérifier si l'utilisateur existe et si le token de vérification est valide
        if ($user && $user->email_verification_token === $verificationToken) {
            // Marquer l'e-mail comme vérifié
            $user->markEmailAsVerified();

            // Rediriger l'utilisateur vers une page de confirmation ou afficher un message de réussite
            return redirect()->route('biicf.login')->with('success', 'Votre compte à été confirmer connectez vous !');
        }

        // Rediriger l'utilisateur vers une page d'erreur ou afficher un message d'erreur
        return redirect()->route('confirmation.error');
    }
    
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileUserController extends Controller
{

    // Méthode pour afficher la vue du profil
    public function showProfile()
    {
        $userConnected = Auth::id(); 
        $user = User::find($userConnected);
    
        // Assurez-vous que $userJoint a une valeur par défaut
        $userJoint = $user && $user->user_joint ? User::find($user->user_joint) : null;
    
        return view('biicf.profile', compact('userConnected', 'userJoint'));
    }
    
    public function userTof(Request $request, $user)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048', // Modifier les types de fichiers acceptés et la taille maximale si nécessaire
        ], [
            'photo.image' => 'Le fichier doit être une image.',
            'photo.required' => 'La photo est obligatoire.',
            'photo.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.',
            'photo.max' => 'La taille maximale de l\'image est de 2 Mo.',
        ]);

        try {
            // Vérifiez si une image est téléchargée avant de la sauvegarder
            if ($request->hasFile('photo')) {
                $photo = $request->file('photo');
                $photoName = time() . '.' . $photo->getClientOriginalExtension();

                // Stockez l'image dans le dossier 'public/post'
                $path = 'post/';
                $photo->move(public_path($path), $photoName);

                // Enregistrez le nom de l'image dans la base de données
                $user->photo = $path . $photoName;
                $user->save();
            }

            return back()->with('success', 'Photo de profil mise à jour avec succès!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour de la photo de profil.'])->withInput();
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = Admin::where('admin_type', 'agent')->get();
        foreach ($agents as $agent) {
            // Récupérer le nombre d'utilisateurs associés à cet agent
            $userCount = $agent->users()->count();
            // Ajouter le nombre d'utilisateurs à l'agent
            $agent->userCount = $userCount;
        }
        return view('admin.agent', ['agents' => $agents]);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|unique:admins,username',
            'password' => 'required|string|min:8',
            'repeat_password' => 'required|string|same:password',
            'phone' => 'required|string',
        ], [
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
            'repeat_password.same' => 'Les mots de passe ne correspondent pas.',
        ]);

        try {
            $admin = new Admin();
            $admin->name = $validatedData['name'] . ' ' . $validatedData['lastname'];
            $admin->username = $validatedData['username'];
            $admin->password = bcrypt($validatedData['password']);
            $admin->phonenumber = $validatedData['phone'];
            $admin->admin_type = 'agent';
            $admin->save();

            return redirect()->route('admin.agent')->with('success', 'Agent ajouté avec succès!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $admin)
    {
        // Trouvez l'agent à supprimer par son ID
        $admin = Admin::findOrFail($admin);

        // Supprimez l'agent
        $admin->delete();

        // Redirigez vers la liste des agents avec un message de succès
        return to_route('admin.index')->with('success', 'L\'agent a été supprimé avec succès.');
    }

    public function updateProfile(Request $request, Admin $admin)
    {
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:admins,username,' . $admin->id,
            'phonenumber' => 'required|string',
        ], [
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
        ]);

        try {
            // Mettre à jour les données de l'administrateur
            $admin->name = $validatedData['name'];
            $admin->username = $validatedData['username'];
            $admin->phonenumber = $validatedData['phonenumber'];
            $admin->save();

            return redirect()->back()->with('success', 'Profil mis à jour avec succès.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Une erreur est survenue lors de la mise à jour du profil.');
        }
    }

    public function updatePassword(Request $request, Admin $admin)
    {
        // Valider les données du formulaire avec les messages d'erreur personnalisés
        $validatedData = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8',
            'new_password_confirmation' => 'required|string|same:new_password',
        ], [
            'new_password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'new_password_confirmation.same' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        // Vérifier si le mot de passe actuel est correct
        if (!Hash::check($validatedData['current_password'], $admin->password)) {
            return redirect()->back()->with('error', 'Mot de passe actuel incorrect.');
        }

        // Mettre à jour le mot de passe de l'administrateur
        $admin->password = Hash::make($validatedData['new_password']);
        $admin->save();

        // Rediriger avec un message de succès
        return redirect()->back()->with('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updateProfilePhoto(Request $request, $adminId)
    {
        // Récupérer l'instance de l'admin
        $admin = Admin::find($adminId);

        if (!$admin) {
            return back()->withErrors(['error' => 'Administrateur non trouvé.'])->withInput();
        }
        
        // Valider les données du formulaire
        $validatedData = $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif', // Modifier les types de fichiers acceptés et la taille maximale si nécessaire
        ], [
            'image.image' => 'Le fichier doit être une image.',
            'image.required' => 'La photo est obligatoire.',
            'image.mimes' => 'Le fichier doit être de type :jpeg, :png, :jpg ou :gif.'
        ]);

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();

            // Stockez l'image dans le dossier 'public/post'
            $path = 'post/';
            $image->move($path, $imageName);

            // Enregistrez le nom de l'image dans la base de données

        }

        try {
            $admin->photo = $request->hasFile('image') ? $path . $imageName : null;
            $admin->save();


            return back()->with('success', 'Photo de profil mise à jour avec succès!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de la mise à jour de la photo de profil.'])->withInput();
        }
    }
}

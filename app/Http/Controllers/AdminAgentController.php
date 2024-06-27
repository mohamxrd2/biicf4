<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Admin;
use App\Models\Wallet;
use App\Models\Transaction;
use Illuminate\Http\Request;

class AdminAgentController extends Controller
{
    //


    // Contrôleur
    public function index()
    {
        

        return view('admin.agent');
    }


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

            // Créer un portefeuille pour l'agent
            $wallet = new Wallet();
            $wallet->admin_id = $admin->id;
            $wallet->balance = 0; // Solde initial
            $wallet->save();

            return redirect()->route('admin.agent')->with('success', 'Agent ajouté avec succès!');
        } catch (\Exception $e) {
            dd($e->getMessage());
            return back()->withErrors(['error' => 'Une erreur est survenue lors de l\'enregistrement.'])->withInput();
        }
    }

    public function destroy(Request $request)
    {
        // Récupérer l'ID de l'agent à supprimer à partir de la requête
        $agentId = $request->input('agent_id');

        // Rechercher l'agent dans la base de données par son ID
        $agent = Admin::findOrFail($agentId);

        // Supprimer l'agent de la base de données
        $agent->delete();

        // Rediriger l'utilisateur vers la page appropriée avec un message de succès
        return back()->with('success', 'Agent supprimé avec succès.');
    }


    public function show($username)
    {
        // Récupérer les détails de l'agent en fonction de son username
        $agent = Admin::where('username', $username)->firstOrFail();

        $adminId = $agent->id;

        $wallet = Wallet::where('admin_id', $agent->id)->first();

        $users = User::where('admin_id', $agent->id)->get();

        // Récupérer le nombre d'utilisateurs ayant le même admin_id que l'agent
        $userCount = User::where('admin_id', $agent->id)->count();

        $transactions = Transaction::with(['senderAdmin', 'receiverAdmin', 'senderUser', 'receiverUser'])
            ->where(function ($query) use ($adminId) {
                $query->where('sender_admin_id', $adminId)
                    ->orWhere('receiver_admin_id', $adminId);
            })
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $transacCount = $transactions->count();

        // Passer les détails de l'agent et les utilisateurs à la vue
        return view('admin.agentShow', compact('agent', 'wallet', 'users', 'userCount', 'adminId', 'transactions', 'transacCount'));
    }
    public function isban(Admin $admin)
    {
        if ($admin->isban) {
            $admin->isban = false; // Débloquer l'agent
            $message = "l'agent a été débloqué avec success";
        } else {
            $admin->isban = true; // Bloquer l'agent
            $message = "l'agent a été bloqué avec success";
        }

        $admin->save();

        return redirect()->back()->with('success', $message);
    }
}

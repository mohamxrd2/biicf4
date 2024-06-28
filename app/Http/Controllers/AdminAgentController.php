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

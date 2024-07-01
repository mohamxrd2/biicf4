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


    
    public function index()
    {
        return view('admin.agent');
    }

    public function show($username)
    {
        // Passer les détails de l'agent et les utilisateurs à la vue
        return view('admin.agentShow', compact('username'));
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

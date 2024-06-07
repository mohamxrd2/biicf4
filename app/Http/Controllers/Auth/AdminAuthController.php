<?php

namespace App\Http\Controllers\Auth;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AdminAuthController extends Controller
{


    public function showLoginForm()
    {
        return view('auth.signin');
    }

    public function login(Request $request)
{
    $credentials = $request->validate([
        'username' => ['required'],
        'password' => ['required', 'string'],
    ], [
        'username.required' => 'Veuillez entrer votre nom d\'utilisateur',
        'password.required' => 'Veuillez entrer votre mot de passe',
    ]);

    $remember = $request->has('remember_me'); // Vérifie si "Remember Me" est coché

    if (Auth::guard('admin')->attempt($credentials, $remember)) {
        return redirect()->intended('/admin/dashboard');
    } else {
        return back()->withErrors([
            'username' => 'Identifiant ou mot de passe incorrect',
        ])->withInput($request->only('username', 'remember_me'));
    }
}


    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }


}

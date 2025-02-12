<?php

namespace App\Livewire;

use App\Models\Admin;
use App\Models\Wallet;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class AddAgents extends Component
{
    use WithPagination;
    public $name;
    public $lastname;
    public $username;
    public $password;
    public $repeat_password;
    public $phone;

    protected $rules = [
        'name' => 'required|string|max:255',
        'lastname' => 'required|string|max:255',
        'username' => 'required|string|unique:admins,username',
        'password' => 'required|string|min:8',
        'repeat_password' => 'required|string|same:password',
        'phone' => 'required|string',
    ];

    protected $messages = [
        'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
        'password.min' => 'Le mot de passe doit contenir au moins 8 caractères',
        'repeat_password.same' => 'Les mots de passe ne correspondent pas.',
    ];

    public function submit()
    {
        $this->validate();

        $admin = new Admin();
        $admin->name = $this->name . ' ' . $this->lastname;
        $admin->username = $this->username;
        $admin->password = bcrypt($this->password);
        $admin->phonenumber = $this->phone;
        $admin->admin_type = 'agent';
        $admin->save();

        // Créer un portefeuille pour l'agent
        $wallet = new Wallet();
        $wallet->admin_id = $admin->id;
        $wallet->balance = 0; // Solde initial
        $wallet->Numero_compte = $this->generateUniqueAccountNumber();
        $wallet->save();

        $this->resetForm();
        // Notification de succès
        $this->dispatch('swal:toast');

        return redirect()->route('admin.agent');
    }

    function generateUniqueAccountNumber()
    {
        do {
            // Génère un numéro aléatoire de 12 chiffres
            $accountNumber = mt_rand(100000000000, 999999999999);

            // Vérifie s'il existe déjà dans la base de données
            $exists = DB::table('wallets')->where('Numero_compte', $accountNumber)->exists();
        } while ($exists);

        return $accountNumber;
    }


    private function resetForm()
    {
        $this->name = '';
        $this->lastname = '';
        $this->username = '';
        $this->password = '';
        $this->repeat_password = '';
        $this->phone = '';
    }


    public function render()
    {
        return view('livewire.add-agents');
    }
}

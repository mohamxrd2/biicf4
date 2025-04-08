<?php

namespace App\Livewire;

use App\Models\Promir;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use GuzzleHttp\Client;

class Profile extends Component
{
    use WithFileUploads;

    public $user,$parrain, $name, $username, $phonenumber, $current_password,
        $new_password, $new_password_confirmation, $image;
    public $liaison_reussie = false;
    protected $listeners = ['liaisonReussie' => 'mettreAJourLiaison'];
    protected $with = ['parrain'];

    public function mount()
    {
        $this->user = auth()->user();

        $this->parrain = User::find(auth()->user()->parrain);

        $this->name = $this->user->name;
        $this->username = $this->user->username;
        $this->phonenumber = $this->user->phone;
        $this->liaison_reussie = Promir::where('user_id', Auth::id())->exists(); // Mettre à true si la liaison est réussie
    }

    // Mise à jour en temps réel après liaison
    public function mettreAJourLiaison()
    {
        $this->liaison_reussie = true;
    }
    private function separerIndicatif($numero)
    {
        $client = new Client();

        try {
            // Récupérer l'indicatif téléphonique à partir du pays de l'utilisateur
            $response = $client->get("https://restcountries.com/v3.1/alpha/{$this->user->country}");
            $data = json_decode($response->getBody()->getContents(), true);

            // Extraire l'indicatif
            $indicatif = $data[0]['idd']['root'] ?? null;
            if (isset($data[0]['idd']['suffixes']) && is_array($data[0]['idd']['suffixes'])) {
                $indicatif .= $data[0]['idd']['suffixes'][0]; // Certains pays ont des suffixes
            }

            // Vérifier si le numéro commence par l'indicatif et le retirer
            if ($indicatif && strpos($numero, $indicatif) === 0) {
                $numero = substr($numero, strlen($indicatif));
            }

            return [
                'indicatif' => $indicatif,
                'numero_principal' => $numero
            ];
        } catch (\Exception $e) {
            return [
                'indicatif' => null,
                'numero_principal' => $numero
            ];
        }
    }
    private function recupererIndicatif($paysCode)
{
    try {
        $client = new Client(['timeout' => 5]);
        $response = $client->get("https://restcountries.com/v3.1/alpha/{$paysCode}");
        $data = json_decode($response->getBody()->getContents(), true);

        $indicatif = $data[0]['idd']['root'] ?? '';
        if (isset($data[0]['idd']['suffixes'][0])) {
            $indicatif .= $data[0]['idd']['suffixes'][0];
        }

        return $indicatif;
    } catch (\Exception $e) {
        return '';
    }
}


    public function LiaisonPromir()
{
    $client = new Client();

    try {
        // Appel avec timeout (max 5 secondes)
        $response = $client->get('https://promi.toopartoo.com/api/users/all', [
            'timeout' => 5
        ]);
        $users = json_decode($response->getBody()->getContents(), true);
    } catch (\Exception $e) {
        $this->dispatch('formSubmitted', "Impossible d'accéder à l'API locale.");
        return;
    }

    // Obtenir l'indicatif de l'utilisateur connecté une seule fois
    $indicatifPrincipal = $this->recupererIndicatif($this->user->country);
    $numeroPrincipal = $this->nettoyerNumero($this->user->phone, $indicatifPrincipal);

    $numeroExiste = false;
    $userTrouve = null;

    foreach ($users['users'] as $user) {
        $numeroUser = $this->nettoyerNumero($user['phone_number'], $indicatifPrincipal);
        if ($numeroUser === $numeroPrincipal) {
            $numeroExiste = true;
            $userTrouve = $user;
            break;
        }
    }

    if ($numeroExiste) {
        if ($userTrouve['mois_depuis_creation'] >= 0) {
            Promir::create([
                'user_id' => auth()->id(),
                'name' => $userTrouve['name'],
                'last_stname' => $userTrouve['last_stname'],
                'user_name' => $userTrouve['user_name'],
                'email' => $userTrouve['email'],
                'phone_number' => $userTrouve['phone_number'],
                'system_client_id' => $userTrouve['system_client_id'],
                'mois_depuis_creation' => $userTrouve['mois_depuis_creation'],
            ]);
            $this->dispatch('liaisonReussie');
        } else {
            $this->dispatch('formSubmitted', "Votre compte doit avoir au moins 3 mois d'ancienneté pour être lié.");
        }
    } else {
        $this->dispatch('formSubmitted', "Le numéro de téléphone {$this->user->phone} n'existe pas dans la liste.");
    }
}
private function nettoyerNumero($numero, $indicatif)
{
    if ($indicatif && strpos($numero, $indicatif) === 0) {
        return substr($numero, strlen($indicatif));
    }
    return $numero;
}


    public function updateProfile()
    {
        $validatedData = $this->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|unique:users,username,' . Auth::id(),
            'phonenumber' => 'required|string',
        ], [
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
        ]);

        // Mettre à jour les données de l'administrateur
        $this->user->update([
            'name' => $this->name,
            'username' => $this->username,
            'phone' => $this->phonenumber,
        ]);

        session()->flash('success', 'Profil mis à jour avec succès.');
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ], [
            'new_password.min' => 'Le mot de passe doit contenir au moins :min caractères.',
            'new_password.confirmed' => 'La confirmation du nouveau mot de passe ne correspond pas.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            session()->flash('error', 'Mot de passe actuel incorrect.');
            return;
        }

        $this->user->update([
            'password' => Hash::make($this->new_password),
        ]);

        session()->flash('success', 'Mot de passe mis à jour avec succès.');
    }

    public function updateProfilePhoto()
    {
        $this->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($this->image) {
            $path = $this->image->store('profile_photos', 'public');
            $this->user->update(['photo' => $path]);
        }

        session()->flash('success', 'Photo de profil mise à jour avec succès!');
    }

    public function updatedName()
    {
        $this->validateOnly('name', [
            'name' => 'required|string|max:255',
        ]);

        $this->user->update(['name' => $this->name]);
        $this->dispatch('profile-updated');
    }

    public function updatedUsername()
    {
        $this->validateOnly('username', [
            'username' => 'required|string|unique:users,username,' . Auth::id(),
        ], [
            'username.unique' => 'Ce nom d\'utilisateur est déjà utilisé.',
        ]);

        $this->user->update(['username' => $this->username]);
        $this->dispatch('profile-updated');
    }

    public function updatedPhonenumber()
    {
        $this->validateOnly('phonenumber', [
            'phonenumber' => 'required|string',
        ]);

        $this->user->update(['phone' => $this->phonenumber]);
        $this->dispatch('profile-updated');
    }

    public function render()
    {
        return view('livewire.profile');
    }
}

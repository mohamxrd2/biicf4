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

    public $user, $parrain, $name, $username, $phonenumber, $current_password, $new_password, $new_password_confirmation, $image;
    public $liaison_reussie = false;
    protected $listeners = ['liaisonReussie' => 'mettreAJourLiaison'];

    public function mount()
    {
        $this->user = User::find(Auth::id());
        $this->parrain = $this->user->parrain ? User::find($this->user->parrain) : null;
        $this->name = $this->user->name;
        $this->username = $this->user->username;
        $this->phonenumber = $this->user->phone;
        $this->liaison_reussie = Promir::where('user_id', Auth::id())->exists();; // Mettre à true si la liaison est réussie

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

    public function LiaisonPromir()
    {
        $client = new Client();
        $response = $client->get('http://127.0.0.1:8001/api/users/all');
        $users = json_decode($response->getBody()->getContents(), true);

        // Séparer le numéro de l'utilisateur actuel
        $numeroRecherche = $this->user->phone;
        $numeroData = $this->separerIndicatif($numeroRecherche);
        $numeroPrincipal = $numeroData['numero_principal'];

        $numeroExiste = false;
        $userTrouve = null;

        // Recherche de l'utilisateur dans la liste
        foreach ($users['users'] as $user) {
            $numeroUserData = $this->separerIndicatif($user['phone_number']);
            if ($numeroUserData['numero_principal'] === $numeroPrincipal) {
                $numeroExiste = true;
                $userTrouve = $user;
                break;
            }
        }

        if ($numeroExiste) {
            // Vérifier si le compte a au moins 3 mois d'ancienneté
            if ($userTrouve['mois_depuis_creation'] >= 3) {
                // Si l'utilisateur existe et est éligible, insérer dans `promir`
                Promir::create([
                    'user_id' => Auth::id(),
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
                $this->dispatch(
                    'formSubmitted',
                    "Votre compte doit avoir au moins 3 mois d'ancienneté pour être lié."
                );
            }
        } else {
            $this->dispatch(
                'formSubmitted',
                "Le numéro de téléphone {$numeroRecherche} n'existe pas dans la liste."
            );
        }
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

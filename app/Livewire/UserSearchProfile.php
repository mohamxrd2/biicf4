<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UserSearchProfile extends Component
{
    public $search = '';
    public $users = [];
    public $user_id;
    public $selectedUserId;

    // Supprimer protected $listeners car on utilise maintenant #[On('xxx')]
    
    public function mount()
    {
        $this->users = [];
    }

    public function updatedSearch()
    {
        if (!empty($this->search)) {
            $currentUserId = auth()->id();
            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId)
                ->get();
        } else {
            $this->users = [];
        }
    }

    public function selectUser($userId, $userName)
    {
        $this->user_id = $userId;
        $this->search = $userName;
        $this->users = [];
    }

    public function addUser()
    {
        $this->validate([
            'user_id' => 'required|exists:users,id',
        ], [
            'user_id.required' => 'Veuillez sélectionner un utilisateur.',
            'user_id.exists' => 'Cet utilisateur n\'existe pas.',
        ]);

        try {
            $userConnected = Auth::id();
            $user = User::findOrFail($userConnected);
            $user->user_joint = $this->user_id;
            $user->save();

            session()->flash('success', 'Utilisateur ajouté avec succès.');
            
            // Réinitialiser le formulaire
            $this->reset(['search', 'user_id', 'users']);
            
            // Dispatch l'événement de rafraîchissement
            $this->dispatch('user-added');
            
            // Rafraîchir le composant
            $this->dispatch('refresh');

        } catch (\Exception $e) {
            session()->flash('error', 'Une erreur est survenue lors de l\'ajout de l\'utilisateur.');
        }
    }

    public function render()
    {
        return view('livewire.user-search-profile');
    }
}
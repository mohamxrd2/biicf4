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

    public function mount()
    {
        $this->users = [];
    }

    public function updatedSearch()
    {
        if (!empty($this->search)) {
            $currentUserId = auth()->id();

            $this->users = User::where('username', 'like', '%' . $this->search . '%')
                ->where('id', '!=', $currentUserId) // Exclure l'utilisateur connecté
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

        // lié l'utlisatteur

        $userConncted = Auth::id();
        $user = User::where('id', $userConncted)->first();

        $user->user_joint = $this->user_id;
        $user->save();

        session()->flash('message', 'Utilisateur ajouté avec succès.');
        $this->resetForm();
    }
    private function resetForm()
    {
        $this->search = '';
    }


    public function render()
    {
        return view('livewire.user-search-profile');
    }
}

<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ListeClients extends Component
{
    use WithPagination;

    public $search = '';
    public $confirmingDeletion = null;

    use WithPagination;
    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function confirmDeletion($id)
    {
        $this->confirmingDeletion = $id;
    }

    public function cancelDeletion()
    {
        $this->confirmingDeletion = null;
    }

    public function delete($id)
    {
        $agent = User::findOrFail($id);
        $agent->delete();

        $this->confirmingDeletion = null;

        // Notification de succès
        $this->dispatch('swal:toast');
    }
    public function render()
    {

        $users = User::with('admin')
            ->orderBy('created_at', 'DESC')
            ->paginate(10);

        $userCount = User::count();

        // Agent
        $adminId = Auth::guard('admin')->id();
        $userAgent = User::where('admin_id', $adminId)
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        $userAgentCount = User::where('admin_id', $adminId)->count();

        return view('livewire.liste-clients', compact('users', 'userCount', 'userAgent', 'userAgentCount'));
    }
}



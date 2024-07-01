<?php

namespace App\Livewire;

use App\Models\Admin;
use Livewire\Component;

class ListeAgents extends Component
{
    public $search = '';
    public $confirmingDeletion = null;


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
        $agent = Admin::findOrFail($id);
        $agent->delete();

        $this->confirmingDeletion = null;

        // Notification de succès
        $this->dispatch('swal:toast');
    }


    public function render()
    {
        // Récupérer tous les agents

        $agents = Admin::where('admin_type', 'agent')
            ->where('name', 'like', "%{$this->search}%")
            ->orderBy('created_at', 'DESC')
            ->paginate(10);
        // Récupérer le nombre total d'agents
        $totalAgents = $agents->count();

        foreach ($agents as $agent) {
            // Récupérer le nombre d'utilisateurs associés à cet agent
            $userCount = $agent->users()->count();
            // Ajouter le nombre d'utilisateurs à l'agent
            $agent->userCount = $userCount;
        }

        return view('livewire.liste-agents', compact('agents', 'totalAgents'));
    }


    public function navigateToaddAgent()
    {
        $this->dispatch('navigate', 'addAgents');
    }
}

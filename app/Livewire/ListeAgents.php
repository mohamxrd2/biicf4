<?php

namespace App\Livewire;

use App\Models\Admin;
use Livewire\Component;

class ListeAgents extends Component
{
    public $search = '';

    public function placeholder()
    {
        return view('admin.components.placeholder');
    }
    public function render()
    {
        // Récupérer tous les agents
        $agents = Admin::where('admin_type', 'agent')
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

        $agents = Admin::latest()
        ->where('name', 'like', "%{$this->search}%")
        ->paginate(5);

        return view('livewire.liste-agents', compact('agents', 'totalAgents'));
    }
}

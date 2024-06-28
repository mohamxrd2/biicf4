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
    // public function delete($id)
    // {
    //    Récupérer l'ID de l'agent à supprimer à partir de la requête
    //    $agentId = $request->input('agent_id');

    //    Rechercher l'agent dans la base de données par son ID
    //    $agent = Admin::findOrFail($agentId);

    //    Supprimer l'agent de la base de données
    //    $agent->delete();

    //    Rediriger l'utilisateur vers la page appropriée avec un message de succès
    //    return back()->with('success', 'Agent supprimé avec succès.');

    //     session()->flash('success', 'La consommation a été supprimée avec succès');
    // }
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

<?php

namespace App\Livewire;

use App\Models\Consommation;
use Livewire\Component;
use Livewire\WithPagination;

class ConsommationList extends Component
{
    use WithPagination;
     public $consommationId='';

    public function delete($id)
    {
        $consommation = Consommation::find($id);

        if (!$consommation) {
            session()->flash('error', 'Consommation non trouvée.');
            return;
        }

        $consommation->delete();

        session()->flash('success', 'La consommation a été supprimée avec succès');
    }

    public function placeholder()
    {
        return view('admin.components.placeholder');
    }

    public function render()
    {
        $consommations = Consommation::where('type', 'produits')->orderBy('created_at', 'DESC')->paginate(5);
        return view('livewire.consommation-list', ['consommations' => $consommations]);
    }
}

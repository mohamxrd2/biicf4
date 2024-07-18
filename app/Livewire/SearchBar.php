<?php

namespace App\Livewire;

use App\Models\ProduitService;
use Livewire\Component;
use Livewire\WithPagination;

class SearchBar extends Component
{
    use WithPagination;

    public $keyword;
    public $zone_economique;
    public $type;
    public $qte_search;

    protected $updatesQueryString = [
        'keyword' => ['except' => ''],
        'zone_economique' => ['except' => ''],
        'type' => ['except' => ''],
    ];

    public function mount()
    {
        $this->fill(request()->only('keyword', 'zone_economique', 'type'));
    }

    public function render()
    {
        $produits = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc');

        if ($this->keyword) {
            $produits->where('name', 'like', '%' . $this->keyword . '%');
        }

        if ($this->zone_economique) {
            $produits->where('zonecoServ', $this->zone_economique);
        }

        if ($this->type) {
            $produits->where('type', $this->type);
        }

        if ($this->qte_search) {
            $produits->where('quantity', '>=', $this->qte_search);
        }

        $results = $produits->paginate(10);

        return view('livewire.search-bar', [
            'produits' => $results,
        ]);
    }
}

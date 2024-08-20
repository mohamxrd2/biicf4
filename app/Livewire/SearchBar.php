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
    public $qte;
    public $prix;

    protected $updatesQueryString = [
        'keyword' => ['except' => ''],
        'zone_economique' => ['except' => ''],
        'type' => ['except' => ''],
        'qte' => ['except' => ''],
        'prix' => ['except' => ''],
    ];

    public function mount()
    {
        $this->fill(request()->only('keyword', 'zone_economique', 'type', 'qte', 'prix'));
    }

    public function render()
    {
        $produits = ProduitService::with('user')
            ->where('statuts', 'Accepté')
            ->orderBy('created_at', 'desc');

        if ($this->keyword) {
            $keyword = strtolower(addslashes($this->keyword));
            $produits->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%']);
        }

        if ($this->zone_economique) {
            $zone_economique = strtolower(addslashes($this->zone_economique));
            $produits->where(function ($query) use ($zone_economique) {
                $query->whereRaw('LOWER(zonecoServ) LIKE ?', ['%' . $zone_economique . '%'])
                    ->orWhereRaw('LOWER(villeServ) LIKE ?', ['%' . $zone_economique . '%'])
                    ->orWhereRaw('LOWER(continent) LIKE ?', ['%' . $zone_economique . '%'])
                    ->orWhereRaw('LOWER(sous_region) LIKE ?', ['%' . $zone_economique . '%'])
                    ->orWhereRaw('LOWER(pays) LIKE ?', ['%' . $zone_economique . '%'])
                    ->orWhereRaw('LOWER(comnServ) LIKE ?', ['%' . $zone_economique . '%']);
            });
        }



        if ($this->type) {
            $produits->where('type', $this->type);
        }

        if ($this->prix) {
            $produits->where('prix', $this->prix);
        }

        if ($this->qte) {
            $produits->where(function ($query) {
                $query->where('qteProd_min', '<=', $this->qte)
                    ->where('qteProd_max', '>=', $this->qte);
            });
        }




        $results = $produits->paginate(10);

        return view('livewire.search-bar', [
            'produits' => $results,
        ]);
    }
}

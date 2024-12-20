<?php

namespace App\Livewire;

use App\Models\ProduitService;
use App\Models\SearchQuery;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Log;

class SearchBar extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    public $keyword = '';
    public $zone_economique = '';
    public $type = '';
    public $qte = '';
    public $prix = '';
    public $resultCount = 0;

    protected $queryString = [
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

    public function updatedKeyword()
    {
        $this->resetPage();
    }

    public function updatedZoneEconomique()
    {
        $this->resetPage();
    }

    public function updatedType()
    {
        $this->resetPage();
    }

    public function updatedQte()
    {
        $this->resetPage();
    }

    public function updatedPrix()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['keyword', 'zone_economique', 'type', 'qte', 'prix']);
        $this->resetPage();
    }

    public function applyFilters()
    {
        $this->resetPage();
    }

    private function getStats()
    {
        return [
            'total_products' => ProduitService::where('statuts', 'Accepté')->count(),
            'total_credits' => ProduitService::where('statuts', 'Accepté')->sum('prix'),
        ];
    }

    public function render()
    {
        try {
            $query = ProduitService::with('user')
                ->where('statuts', 'Accepté')
                ->orderBy('created_at', 'desc');

            // Filtrage par mot-clé
            if (!empty($this->keyword)) {
                $keyword = strtolower(trim($this->keyword));
                $query->where(function($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%'])
                      ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
                });
            }

            // Filtrage par zone économique
            if (!empty($this->zone_economique) && auth()->check()) {
                $user = auth()->user();
                $userLocation = [
                    'commune' => strtolower($user->commune),
                    'ville' => strtolower($user->ville),
                    'departement' => strtolower($user->departe),
                    'pays' => strtolower($user->country),
                    'sous_region' => strtolower($user->sous_region),
                    'continent' => strtolower($user->continent),
                ];

                $zoneMap = [
                    'proximite' => ['field' => 'commune', 'value' => $userLocation['commune']],
                    'locale' => ['field' => 'ville', 'value' => $userLocation['ville']],
                    'departementale' => ['field' => 'departement', 'value' => $userLocation['departement']],
                    'nationale' => ['field' => 'pays', 'value' => $userLocation['pays']],
                    'sous_regionale' => ['field' => 'sous_region', 'value' => $userLocation['sous_region']],
                    'continentale' => ['field' => 'continent', 'value' => $userLocation['continent']],
                ];

                if (isset($zoneMap[$this->zone_economique])) {
                    $zone = $zoneMap[$this->zone_economique];
                    $query->whereHas('user', function($q) use ($zone) {
                        $q->whereRaw("LOWER({$zone['field']}) = ?", [$zone['value']]);
                    });
                }
            }

            // Filtrage par type
            if (!empty($this->type)) {
                $query->where('type', $this->type);
            }

            // Filtrage par prix
            if (!empty($this->prix)) {
                $query->where('prix', '<=', $this->prix);
            }

            // Filtrage par quantité
            if (!empty($this->qte)) {
                $query->where(function ($q) {
                    $q->where('qteProd_min', '<=', $this->qte)
                      ->where('qteProd_max', '>=', $this->qte);
                });
            }

            // Pagination
            $produits = $query->paginate(12);
            $this->resultCount = $produits->total();

            // Recherches populaires
            $popularSearches = SearchQuery::select('query', DB::raw('COUNT(*) as count'))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            // Statistiques
            $stats = $this->getStats();

            return view('livewire.search-bar', [
                'produits' => $produits,
                'resultCount' => $this->resultCount,
                'popularSearches' => $popularSearches,
                'stats' => $stats,
            ]);

        } catch (Exception $e) {
            Log::error('Erreur lors de la recherche', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('livewire.search-bar', [
                'produits' => collect(),
                'resultCount' => 0,
                'popularSearches' => collect(),
                'stats' => $this->getStats(),
            ]);
        }
    }
}
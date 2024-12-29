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
    public $error = null;

    protected $queryString = [
        'keyword' => ['except' => ''],
        'zone_economique' => ['except' => ''],
        'type' => ['except' => ''],
        'qte' => ['except' => ''],
        'prix' => ['except' => ''],
    ];

    public function mount()
    {
        try {
            $this->fill(request()->only('keyword', 'zone_economique', 'type', 'qte', 'prix'));
        } catch (Exception $e) {
            Log::error('Erreur lors du montage du composant', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = "Une erreur est survenue lors de l'initialisation des filtres.";
        }
    }

    private function resetPage()
    {
        try {
            parent::resetPage();
        } catch (Exception $e) {
            Log::error('Erreur lors de la réinitialisation de la page', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function resetFilters()
    {
        try {
            $this->reset(['keyword', 'zone_economique', 'type', 'qte', 'prix']);
            $this->resetPage();
        } catch (Exception $e) {
            Log::error('Erreur lors de la réinitialisation des filtres', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->error = "Une erreur est survenue lors de la réinitialisation des filtres.";
        }
    }

    private function getStats()
    {
        try {
            return [
                'total_products' => ProduitService::where('statuts', 'Accepté')->count(),
                'total_credits' => ProduitService::where('statuts', 'Accepté')->sum('prix'),
            ];
        } catch (Exception $e) {
            Log::error('Erreur lors de la récupération des statistiques', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return [
                'total_products' => 0,
                'total_credits' => 0,
            ];
        }
    }

    private function applyKeywordFilter($query, $keyword)
    {
        try {
            return $query->where(function($q) use ($keyword) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
            });
        } catch (Exception $e) {
            Log::error('Erreur lors du filtrage par mot-clé', [
                'keyword' => $keyword,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    private function applyZoneFilter($query, $user, $userLocation, $zoneMap)
    {
        try {
            if (isset($zoneMap[$this->zone_economique])) {
                $zone = $zoneMap[$this->zone_economique];
                return $query->whereHas('user', function($q) use ($zone) {
                    $q->whereRaw("LOWER({$zone['field']}) = ?", [$zone['value']]);
                });
            }
            return $query;
        } catch (Exception $e) {
            Log::error('Erreur lors du filtrage par zone', [
                'zone' => $this->zone_economique,
                'message' => $e->getMessage()
            ]);
            throw $e;
        }
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
                $query = $this->applyKeywordFilter($query, $keyword);
            }

            // Filtrage par zone économique
            if (!empty($this->zone_economique) && auth()->check()) {
                try {
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
                        'departementale' => ['field' => 'departe', 'value' => $userLocation['departement']],
                        'nationale' => ['field' => 'country', 'value' => $userLocation['pays']],
                        'sous_regionale' => ['field' => 'sous_region', 'value' => $userLocation['sous_region']],
                        'continentale' => ['field' => 'continent', 'value' => $userLocation['continent']],
                    ];

                    $query = $this->applyZoneFilter($query, $user, $userLocation, $zoneMap);
                } catch (Exception $e) {
                    Log::error('Erreur lors du filtrage par zone économique', [
                        'message' => $e->getMessage()
                    ]);
                }
            }

            // Autres filtres avec gestion d'erreurs
            try {
                if (!empty($this->type)) {
                    $query->where('type', $this->type);
                }

                if (!empty($this->prix)) {
                    $query->where('prix', '<=', $this->prix);
                }

                if (!empty($this->qte)) {
                    $query->where(function ($q) {
                        $q->where('qteProd_min', '<=', $this->qte)
                          ->where('qteProd_max', '>=', $this->qte);
                    });
                }
            } catch (Exception $e) {
                Log::error('Erreur lors de l\'application des filtres', [
                    'message' => $e->getMessage()
                ]);
            }

            // Pagination et comptage
            try {
                $produits = $query->paginate(12);
                $this->resultCount = $produits->total();
            } catch (Exception $e) {
                Log::error('Erreur lors de la pagination', [
                    'message' => $e->getMessage()
                ]);
                $produits = collect();
                $this->resultCount = 0;
            }

            // Recherches populaires
            try {
                $popularSearches = SearchQuery::select('query', DB::raw('COUNT(*) as count'))
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get();
            } catch (Exception $e) {
                Log::error('Erreur lors de la récupération des recherches populaires', [
                    'message' => $e->getMessage()
                ]);
                $popularSearches = collect();
            }

            return view('livewire.search-bar', [
                'produits' => $produits,
                'resultCount' => $this->resultCount,
                'popularSearches' => $popularSearches,
                'stats' => $this->getStats(),
                'error' => $this->error,
            ]);

        } catch (Exception $e) {
            Log::error('Erreur critique lors du rendu', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return view('livewire.search-bar', [
                'produits' => collect(),
                'resultCount' => 0,
                'popularSearches' => collect(),
                'stats' => $this->getStats(),
                'error' => "Une erreur est survenue lors du chargement des résultats.",
            ]);
        }
    }
}

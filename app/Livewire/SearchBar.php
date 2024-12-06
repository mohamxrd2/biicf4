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

    public $keyword;
    public $zone_economique;
    public $type;
    public $qte;
    public $prix;
    public $searchResults;

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

    public $modalOpen;

    public function accepter()
    {
        $this->modalOpen = false;
    }

    public function search()
    {
        // Cette méthode est appelée lors de la soumission du formulaire
        Log::info('Recherche déclenchée', [
            'keyword' => $this->keyword,
            'zone_economique' => $this->zone_economique,
            'type' => $this->type,
            'qte' => $this->qte,
            'prix' => $this->prix,
        ]);

        // Stocker les informations de recherche
        $this->searchResults = [
            'keyword' => $this->keyword,
            'zone_economique' => $this->zone_economique,
            'type' => $this->type,
            'qte' => $this->qte,
            'prix' => $this->prix,
        ];

        SearchQuery::create([
            'query' => $this->keyword,
        ]);
    }

    public function render()
    {
        try {
            // Initialiser la requête
            $produits = ProduitService::with('user')
                ->where('statuts', 'Accepté')
                ->orderBy('created_at', 'desc');

            // Filtrage par mot-clé
            if ($this->keyword) {
                $keyword = strtolower(trim($this->keyword));
                $produits->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%']);
            }

            // Charger les informations de l'utilisateur
            $user = auth()->user();
            if ($user && $this->zone_economique) {
                $normalizedZoneEconomique = strtolower(trim($this->zone_economique));

                // Normaliser les informations de localisation de l'utilisateur
                $userLocation = [
                    'commune' => strtolower($user->commune),
                    'ville' => strtolower($user->ville),
                    'departement' => strtolower($user->departe),
                    'pays' => strtolower($user->country),
                    'sous_region' => strtolower($user->sous_region),
                    'continent' => strtolower($user->continent),
                ];

                // Appliquer le filtre en fonction de la zone choisie
                switch ($normalizedZoneEconomique) {
                    case 'proximite':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(commune) = ?', [$userLocation['commune']]));
                        break;
                    case 'locale':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(ville) = ?', [$userLocation['ville']]));
                        break;
                    case 'departementale':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(departement) = ?', [$userLocation['departement']]));
                        break;
                    case 'nationale':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(pays) = ?', [$userLocation['pays']]));
                        break;
                    case 'sous_regionale':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(sous_region) = ?', [$userLocation['sous_region']]));
                        break;
                    case 'continentale':
                        $produits->whereHas('user', fn($query) => $query->whereRaw('LOWER(continent) = ?', [$userLocation['continent']]));
                        break;
                    default:
                        Log::warning('Zone économique non reconnue', ['zone_economique' => $this->zone_economique]);
                        break;
                }
            }

            // Filtrage par type
            if ($this->type) {
                $produits->where('type', $this->type);
            }

            // Filtrage par prix
            if ($this->prix) {
                $produits->where('prix', '<=', $this->prix);
            }

            // Filtrage par quantité
            if ($this->qte) {
                $produits->where(function ($query) {
                    $query->where('qteProd_min', '<=', $this->qte)
                        ->where('qteProd_max', '>=', $this->qte);
                });
            }

            // Pagination des résultats
            $results = $produits->paginate(10);

            // Récupération des requêtes de recherche les plus fréquentes
            $searchQueries = SearchQuery::select('query', DB::raw('COUNT(*) as count'))
                ->groupBy('query')
                ->orderBy('count', 'desc')
                ->limit(5)
                ->get();

            // Journalisation des résultats
            Log::info('Résultats paginés récupérés', ['results_count' => $results->count()]);

            return view('livewire.search-bar', [
                'produits' => $results,
                'searchQueries' => $searchQueries,
            ]);
        } catch (Exception $e) {
            // Gestion des erreurs et journalisation
            Log::error('Erreur lors du rendu de la recherche', ['error' => $e->getMessage()]);
            return view('livewire.search-bar', [
                'produits' => collect(), // Retourne une collection vide en cas d'erreur
                'searchQueries' => collect(),
            ]);
        }
    }
}

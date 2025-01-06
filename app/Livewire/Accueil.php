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

class Accueil extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';
    protected $layout = 'components.layouts.app';

    public $keyword = '';
    public $zone_economique = '';
    public $type = '';
    public $qte = '';
    public $prix = '';
    public $resultCount = 0;
    public $error = null;

    public function render()
    {
        try {
            $query = ProduitService::with('user')
                ->where('statuts', 'Accepté');

            // Exclure les produits de l'utilisateur connecté
            if (auth()->check()) {
                $query->where('user_id', '!=', auth()->id());
            }

            $query->orderBy('created_at', 'desc');

            // Filtre par mot-clé
            if (!empty($this->keyword)) {
                $keyword = strtolower(trim($this->keyword));
                $query->where(function ($q) use ($keyword) {
                    $q->whereRaw('LOWER(name) LIKE ?', ['%' . $keyword . '%'])
                        ->orWhereRaw('LOWER(description) LIKE ?', ['%' . $keyword . '%']);
                });
            }

            // Filtre par zone économique
            if (!empty($this->zone_economique) && auth()->check()) {
                $user = auth()->user();
                $zoneMap = [
                    'proximite' => ['field' => 'commune', 'value' => $user->commune],
                    'locale' => ['field' => 'ville', 'value' => $user->ville],
                    'departementale' => ['field' => 'departe', 'value' => $user->departe],
                    'nationale' => ['field' => 'country', 'value' => $user->country],
                    'sous_regionale' => ['field' => 'sous_region', 'value' => $user->sous_region],
                    'continentale' => ['field' => 'continent', 'value' => $user->continent],
                ];

                if (isset($zoneMap[$this->zone_economique])) {
                    $zone = $zoneMap[$this->zone_economique];
                    $query->whereHas('user', function ($q) use ($zone) {
                        $q->where($zone['field'], $zone['value']);
                    });
                }
            }

            // Filtre par type
            if (!empty($this->type)) {
                $query->where('type', $this->type);
            }

            // Filtre par prix
            if (!empty($this->prix)) {
                $query->where('prix', '<=', $this->prix);
            }

            // Filtre par quantité
            if (!empty($this->qte)) {
                $query->where(function ($q) {
                    $q->where('qteProd_min', '<=', $this->qte)
                        ->where('qteProd_max', '>=', $this->qte);
                });
            }

            $produits = $query->paginate(12);
            $this->resultCount = $produits->total();

            return view('livewire.acceuil', [
                'produits' => $produits,
                'resultCount' => $this->resultCount,
                'popularSearches' => SearchQuery::select('query', DB::raw('COUNT(*) as count'))
                    ->groupBy('query')
                    ->orderBy('count', 'desc')
                    ->limit(5)
                    ->get(),
                'stats' => [
                    'total_products' => ProduitService::where('statuts', 'Accepté')->count(),
                    'total_credits' => ProduitService::where('statuts', 'Accepté')->sum('prix'),
                ],
            ]);

        } catch (Exception $e) {
            Log::error('Erreur critique lors du rendu', [
                'message' => $e->getMessage()
            ]);

            return view('livewire.acceuil', [
                'produits' => collect(),
                'resultCount' => 0,
                'popularSearches' => collect(),
                'stats' => ['total_products' => 0, 'total_credits' => 0],
                'error' => "Une erreur est survenue lors du chargement des résultats."
            ]);
        }
    }
}

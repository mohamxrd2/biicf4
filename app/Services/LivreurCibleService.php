<?php

namespace App\Services;

use App\Models\User;
use App\Models\Livraisons;
use Illuminate\Support\Facades\Log;

class LivreurCibleService
{
    /**
     * Cible les livreurs en fonction des critères géographiques du client
     *
     * @param int|null $idSender ID de l'expéditeur
     * @return array|null Tableau de résultats ou null si erreur
     */
    public function targeterLivreurs(?int $idSender)
    {
        // Vérification de l'existence de l'expéditeur
        if (!$idSender) {
            Log::warning('Ciblage livreurs: ID expéditeur non défini');
            return null;
        }

        // Récupérer les informations du client
        $client = User::find($idSender);
        if (!$client) {
            Log::warning("Ciblage livreurs: Client introuvable (ID: {$idSender})");
            return null;
        }

        // Normalisation des données géographiques
        $criteres = [
            'continent' => strtolower($client->continent),
            'sous_region' => strtolower($client->sous_region),
            'pays' => strtolower($client->country),
            'departement' => strtolower($client->departe),
            'commune' => strtolower($client->commune)
        ];

        // Requête de ciblage des livreurs
        $livreurs = Livraisons::where('etat', 'Accepté')
            ->where(function ($query) use ($criteres) {
                $query
                    // Zone de proximité
                    ->orWhere(function ($q) use ($criteres) {
                        $q->where('zone', 'proximite')
                            ->where('continent', $criteres['continent'])
                            ->where('sous_region', $criteres['sous_region'])
                            ->where('pays', $criteres['pays'])
                            ->where('departe', $criteres['departement'])
                            ->where('commune', $criteres['commune']);
                    })
                    // Zone locale
                    ->orWhere(function ($q) use ($criteres) {
                        $q->where('zone', 'locale')
                            ->where('continent', $criteres['continent'])
                            ->where('sous_region', $criteres['sous_region'])
                            ->where('pays', $criteres['pays'])
                            ->where('departe', $criteres['departement']);
                    })
                    // Zone nationale
                    ->orWhere(function ($q) use ($criteres) {
                        $q->where('zone', 'nationale')
                            ->where('continent', $criteres['continent'])
                            ->where('sous_region', $criteres['sous_region']);
                    })
                    // Zone sous-régionale
                    ->orWhere(function ($q) use ($criteres) {
                        $q->where('zone', 'sous_regionale')
                            ->where('continent', $criteres['continent']);
                    })
                    // Zone continentale
                    ->orWhere('zone', 'continentale');
            })
            ->get();

        // Préparer les résultats
        $resultat = [
            'livreurs' => $livreurs,
            'livreurs_ids' => $livreurs->pluck('user_id'),
            'total_livreurs' => $livreurs->count(),
            'criteres' => $criteres
        ];

        // Journalisation du résultat
        Log::info('Ciblage livreurs terminé', [
            'total_livreurs' => $resultat['total_livreurs'],
            'criteres' => $criteres
        ]);

        return $resultat;
    }
}

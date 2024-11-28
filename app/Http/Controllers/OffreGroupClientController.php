<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Consommation;
use App\Notifications\OffreNotifGroup;
use App\Models\ProduitService;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class OffreGroupClientController extends Controller
{
    public function sendoffGrp(Request $request)
    {
        $user_id = Auth::guard('web')->id();

        // Validation des données d'entrée
        $validated = $request->validate([
            'produit_id' => 'required|integer|exists:produit_services,id',
            'zone_economique' => 'required|string|in:proximite,locale,departementale,nationale,sous_regionale,continentale',
        ]);

        try {
            // Récupération des données validées
            $produitId = $validated['produit_id'];
            $zone_economique = strtolower($validated['zone_economique']);

            // Récupérer l'utilisateur courant
            $user = User::find($user_id);
            if (!$user) {
                Log::error('Utilisateur non trouvé', ['user_id' => $user_id]);
                return redirect()->back()->with('error', 'Utilisateur non trouvé.');
            }

            // Récupérer le produit
            $produit = ProduitService::findOrFail($produitId);
            $referenceProduit = $produit->reference;

            Log::info('Produit récupéré', [
                'produit_id' => $produitId,
                'reference' => $referenceProduit,
            ]);

            // Générer un code unique
            $code_unique = $this->generateUniqueReference();

            // Récupérer les utilisateurs consommant ce produit
            $idsProprietaires = $this->getConsommateurs($referenceProduit, $user_id);
            if (empty($idsProprietaires)) {
                Log::warning('Aucun utilisateur consommateur trouvé', ['produit' => $referenceProduit]);
                return redirect()->back()->with('error', 'Aucun utilisateur ne consomme ce produit.');
            }

            // Appliquer le filtre de zone économique
            $appliedZoneValue = $this->getZoneValue($zone_economique, $user);
            if (!$appliedZoneValue) {
                Log::error('Zone économique invalide', ['zone' => $zone_economique]);
                return redirect()->back()->with('error', 'Zone économique invalide.');
            }

            // Filtrer les utilisateurs par zone
            $idsLocalite = $this->getUsersInZone($idsProprietaires, $appliedZoneValue);
            if (empty($idsLocalite)) {
                Log::warning('Aucun utilisateur trouvé dans la zone économique', [
                    'zone' => $zone_economique,
                    'value' => $appliedZoneValue,
                ]);
                return redirect()->back()->with('error', 'Aucun utilisateur trouvé dans votre zone économique.');
            }

            // Fusionner les IDs pour éviter les doublons
            $idsToNotify = array_unique(array_merge($idsProprietaires, $idsLocalite));
            Log::info('IDs à notifier', ['ids' => $idsToNotify]);

            // Envoyer des notifications
            $this->notifyUsers($idsToNotify, $produit, $code_unique);

            return redirect()->back()->with('success', 'Notifications envoyées avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'envoi des notifications', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Une erreur s\'est produite. Veuillez réessayer.');
        }
    }

    /**
     * Récupérer les IDs des consommateurs d'un produit.
     */
    private function getConsommateurs(string $referenceProduit, int $user_id): array
    {
        return Consommation::where('reference', $referenceProduit)
            ->where('id_user', '!=', $user_id)
            ->where('statuts', 'Accepté')
            ->distinct()
            ->pluck('id_user')
            ->toArray();
    }

    /**
     * Récupérer la valeur de la zone économique en fonction de l'utilisateur.
     */
    private function getZoneValue(string $zone_economique, User $user): ?string
    {
        $mapping = [
            'proximite' => strtolower($user->commune),
            'locale' => strtolower($user->ville),
            'departementale' => strtolower($user->departe),
            'nationale' => strtolower($user->country),
            'sous_regionale' => strtolower($user->sous_region),
            'continentale' => strtolower($user->continent),
        ];
        return $mapping[$zone_economique] ?? null;
    }

    /**
     * Récupérer les IDs des utilisateurs dans une zone donnée.
     */
    private function getUsersInZone(array $idsProprietaires, string $appliedZoneValue): array
    {
        return User::whereIn('id', $idsProprietaires)
            ->where(function ($query) use ($appliedZoneValue) {
                $query->where('commune', $appliedZoneValue)
                    ->orWhere('ville', $appliedZoneValue)
                    ->orWhere('departe', $appliedZoneValue)
                    ->orWhere('country', $appliedZoneValue)
                    ->orWhere('sous_region', $appliedZoneValue)
                    ->orWhere('continent', $appliedZoneValue);
            })
            ->pluck('id')
            ->toArray();
    }

    /**
     * Envoyer des notifications aux utilisateurs spécifiés.
     */
    private function notifyUsers(array $idsToNotify, ProduitService $produit, string $code_unique): void
    {
        foreach ($idsToNotify as $userId) {
            $user = User::find($userId);
            if ($user) {
                Notification::send($user, new OffreNotifGroup($produit, $code_unique));
                Log::info('Notification envoyée', ['user_id' => $userId]);
            } else {
                Log::warning('Utilisateur non trouvé pour notification', ['user_id' => $userId]);
            }
        }
    }

    protected function generateUniqueReference()
    {
        return 'REF-' . strtoupper(Str::random(6)); // Exemple de génération de référence
    }
}

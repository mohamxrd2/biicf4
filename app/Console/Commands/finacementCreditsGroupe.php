<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\AjoutMontant;
use App\Models\Cfa;
use App\Models\credits;
use App\Models\credits_groupé;
use App\Models\Crp;
use App\Models\DemandeCredi;
use App\Models\portions_journalieres;
use App\Models\Projet;
use App\Models\projets_accordé;
use App\Models\remboursements;
use App\Models\Transaction;
use App\Models\transactions_remboursement;
use App\Models\User;
use App\Models\Wallet;
use App\Notifications\PortionJournaliere;
use Carbon\Carbon;
use Exception;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class finacementCreditsGroupe extends Command
{

    protected $signature = 'app:finacementCreditsGroupe';
    protected $description = 'Récupérer les credits deja validés';


    public function handle()
    {
        // Récupérer la date actuelle
        $dateActuelle = Carbon::today();
        Log::info('Date actuelle récupérée: ' . $dateActuelle);

        // Récupérer les credits où 'count' est égal à true et dont la date de fin est passée
        $credits = DemandeCredi::where('count', true)
            ->whereDate('date_fin', '<', $dateActuelle)
            ->where('status',  'en cours') // Exclure ceux dont le statut est 'terminer'
            ->where('type_financement', 'offre-composite')
            ->whereNotNull('bailleur')
            ->select('id', 'demande_id', 'id_user', 'date_fin', 'duree', 'montant', 'taux', 'status')
            ->distinct() // Récupérer des résultats distincts
            ->get()
            ->groupBy('demande_id') // Grouper par 'demande_id'
            ->map(function ($group) {
                return $group->first(); // Utiliser le premier élément de chaque groupe
            });
        Log::info('credits récupérés groupe: ' . $credits->count() . ' credits');

        // Tableau pour stocker les résultats
        $resultatsInvestisseurs = [];

        // Parcourir les credits sélectionnés pour remplir la nouvelle table
        foreach ($credits as $credit) {
            Log::info('Traitement du crédit ID: ' . $credit->id . ' pour l\'utilisateur ID: ' . $credit->id_user);

            // Récupérer les montants financés par chaque investisseur pour le credit en sommant les montants si plusieurs enregistrements existent
            $investissements = AjoutMontant::where('id_demnd_credit', $credit->id)
                ->select('id_invest', DB::raw('SUM(montant) as total_montant'))
                ->groupBy('id_invest') // Regroupe par id_invest pour sommer les montants multiples
                ->get();

            Log::info('Investissements associés au credit ID ' . $credit->id . ': ' . $investissements->count() . ' investisseurs (somme totale par investisseur)');

            // Insertion dans la table projets_accordés
            try {
                // Assurez-vous que les dates sont bien des instances de Carbon

                $debut = Carbon::parse($credit->date_fin);
                $durer = Carbon::parse($credit->duree);
                $jours = $debut->diffInDays($durer);
                Log::info('nbre date:' . $jours);

                $montantTotal = $credit->montant  * (1 + $credit->taux / 100);
                $portion_journaliere = $jours > 0 ? $montantTotal  / $jours : 0;

                Log::info('credit user ID: ' . $credit->id_user);

                credits_groupé::create([
                    'emprunteur_id' => $credit->id_user, // Assurez-vous que la relation est bien définie
                    'investisseurs' => json_encode($resultatsInvestisseurs), // Convertir les investisseurs en JSON
                    'montant' => $montantTotal,
                    'montan_restantt' => $montantTotal, // Assurez-vous que ce champ existe dans la table 'credits'
                    'taux_interet' => $credit->taux,
                    'date_debut' => $debut,
                    'date_fin' => $durer,
                    'portion_journaliere' => $portion_journaliere,
                    'statut' => 'en cours',
                ]);

                // Après avoir inséré toutes les informations pour un demande_id donné,
                // mettre à jour le statut de tous les crédits ayant le même demande_id à "terminer".
                DemandeCredi::where('demande_id', $credit->demande_id)->update(['status' => 'terminer']);


                Log::info('Projet ID: ' . $credit->id . ' inséré dans la table projets_accordés');
            } catch (Exception $e) {
                Log::error('Erreur lors de l\'insertion du credit ID ' . $credit->id . ' dans la table projets_accordés: ' . $e->getMessage());
            }

            // Parcourir les investissements pour chaque investisseur dans le credit
            foreach ($investissements as $investissement) {
                // Stocker dans le tableau les informations sur le credit, l'investisseur et le montant total financé
                $resultatsInvestisseurs[] = [
                    'id_demnd_credit' => $credit->id,
                    'investisseur_id' => $investissement->id_invest,
                    'montant_finance' => $investissement->total_montant, // Montant total financé par cet investisseur
                ];
                // Création du remboursement associé
                remboursements::create([
                    'creditGrp_id' => $credit->id,  // Associe le remboursement au crédit créé
                    'id_user' => $investissement->id_invest,  // Associe le remboursement au crédit créé
                    'montant_capital' => $investissement->total_montant,  // Définissez cette variable en fonction de votre logique métier
                    'montant_interet' => $credit->taux,  // Définissez cette variable en fonction de votre logique métier
                    'date_remboursement' => $credit->duree,  // Définissez cette variable en fonction de votre logique métier
                    'statut' => 'en cours',  // Statut du remboursement
                    'description' => $credit->objet_financement,  // Statut du remboursement
                ]);
                // Log des informations sur chaque investisseur et son montant total financé
                Log::info('Investisseur ID: ' . $investissement->id_invest . ' a financé un total de ' . $investissement->total_montant . ' pour le credit ID: ' . $credit->id);
            }
        }

        //Log de fin de traitement
        Log::info('Traitement des credits terminé. Nombre total de résultats d\'investisseurs: ' . count($resultatsInvestisseurs));
    }
}

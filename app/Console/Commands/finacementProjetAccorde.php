<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Events\PortionUpdated;
use App\Models\AjoutAction;
use App\Models\AjoutMontant;
use App\Models\Cfa;
use App\Models\credits;
use App\Models\Crp;
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

class finacementProjetAccorde extends Command
{

    protected $signature = 'app:finacementProjetAccorde';
    protected $description = 'Récupérer les credits deja validés';


    public function handle()
    {
        // Récupérer la date actuelle
        $dateActuelle = Carbon::today();

        // Récupérer les projets où 'count' est égal à true et dont la date de fin est passée
        $projets = Projet::where('count', true)
            ->whereDate('date_fin', '<', $dateActuelle)
            ->where('etat', 'en cours')
            ->where('type_financement', 'groupé')
            ->get();

        // Tableau pour stocker les résultats
        $resultatsInvestisseurs = [];
        $actionsData = [];

        // Parcourir les projets sélectionnés pour remplir la nouvelle table
        foreach ($projets as $projet) {

            // Récupérer les montants financés par chaque investisseur pour le projet en sommant les montants si plusieurs enregistrements existent
            $investissements = AjoutMontant::where('id_projet', $projet->id)
                ->select('id_invest', DB::raw('SUM(montant) as total_montant'))
                ->groupBy('id_invest') // Regroupe par id_invest pour sommer les montants multiples
                ->get();

            // Parcourir les investissements pour chaque investisseur dans le projet
            foreach ($investissements as $investissement) {
                // Stocker dans le tableau les informations sur le projet, l'investisseur et le montant total financé
                $resultatsInvestisseurs[] = [
                    'projet_id' => $projet->id,
                    'investisseur_id' => $investissement->id_invest,
                    'montant_finance' => $investissement->total_montant, // Montant total financé par cet investisseur
                ];
            }

            //Vérifier si des actions ont été prises pour le même projet dans la table AjoutAction
            $actions = AjoutAction::where('id_projet', $projet->id)
                ->select('id_invest', DB::raw('SUM(montant) as total_montant'), DB::raw('SUM(nombreActions) as nombre_actions'))
                ->groupBy('id_invest') // Regroupe par id_invest pour sommer les montants multiples
                ->get();


            // Si des actions existent, les ajouter à un tableau JSON
            if ($actions->count() > 0) {

                foreach ($actions as $action) {

                    // Ajouter chaque action au tableau avec des informations pertinentes
                    $actionsData[] = [
                        'projet_id' => $projet->id,
                        'investisseur_id' => $action->id_invest,
                        'montant_finance' => $action->total_montant,
                        'nombreActions' => $action->nombre_actions,
                    ];
                }
            }

            // Insertion dans la table projets_accordés
            try {
                // Assurez-vous que les dates sont bien des instances de Carbon
                $debut = Carbon::parse($projet->date_fin);
                $durer = Carbon::parse($projet->duree);
                $jours = $debut->diffInDays($durer);

                $montantComission = $projet->montant  * 0.01;
                $montantTotal = ($projet->montant  * (1 + $projet->taux / 100)) + $montantComission;
                $portion_journaliere = ($jours > 0) ? ($montantTotal + $montantComission)  / $jours : 0;


                projets_accordé::create([
                    'emprunteur_id' => $projet->id_user, // Assurez-vous que la relation est bien définie
                    'investisseurs' => !empty($resultatsInvestisseurs) ? json_encode($resultatsInvestisseurs) : null, // Convertir les investisseurs en JSON
                    'montant' => $montantTotal,
                    'montan_restantt' => $montantTotal, // Assurez-vous que ce champ existe dans la table 'projets'
                    'action' =>  !empty($actionsData) ? json_encode($actionsData) : null,
                    'taux_interet' => $projet->taux,
                    'date_debut' => $debut,
                    'date_fin' => $durer,
                    'portion_journaliere' => $portion_journaliere,
                    'comission' => $montantComission,
                    'statut' => 'en cours',
                    'description' => $projet->name,
                ]);
            } catch (Exception $e) {
                Log::error('Erreur lors de l\'insertion du projet ID ' . $projet->id . ' dans la table projets_accordés: ' . $e->getMessage());
            }

            // Parcourir les investissements pour chaque investisseur dans le projet
            foreach ($investissements as $investissement) {
                remboursements::create([
                    'projet_id' => $projet->id,  // Associe le remboursement au crédit créé
                    'id_user' => $investissement->id_invest,  // Associe le remboursement au crédit créé
                    'montant_capital' => $investissement->total_montant,
                    'montant_interet' => $projet->taux,
                    'date_remboursement' => $projet->duree,
                    'statut' => 'en cours',  // Statut du remboursement
                    'description' => $projet->objet_financement,  // Statut du remboursement
                ]);
            }

            // Mise à jour de l'état du projet
            $projet = Projet::findOrFail($projet->id); // Vérifie que le projet existe
            $projet->update(['etat' => 'terminer']);  // Met à jour l'état du projet

        }
    }
}

<?php

namespace Tests\Feature;

use App\Models\EchecPaiement;
use App\Models\Tontines;
use App\Models\TontineUser;
use App\Models\User;
use App\Models\Cotisation;
use App\Models\Wallet;
use App\Models\gelement;
use App\Notifications\tontinesNotification;
use App\Services\TransactionService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Str;
use Tests\TestCase;

class TontineEpargneTest extends TestCase
{
    protected $transactionService;

    public function setUp(): void
    {
        parent::setUp();
        $this->transactionService = app(TransactionService::class);
    }

    private function generateUniqueReference(): string
    {
        return 'REF-' . strtoupper(Str::random(6));
    }

    protected function generateIntegerReference(): int
    {
        return now()->timestamp * 1000 + now()->micro;
    }

    public function test_multiple_users_multiple_tontines()
    {
        // Mock time to start testing
        $startTestDate = Carbon::create(2025, 2, 19, 9, 0, 0);
        Carbon::setTestNow($startTestDate);

        // Créer plusieurs utilisateurs
        $users = collect([
            ['id' => 121, 'initial_balance' => 100],
            // ['id' => 122, 'initial_balance' => 800],
            // ['id' => 123, 'initial_balance' => 1200]
        ])->map(function ($userData) {
            $user = User::find($userData['id']);
            if (!$user) {
                Log::error("Utilisateur {$userData['id']} non trouvé.");
                return null;
            }

            // Créer ou mettre à jour le wallet
            $wallet = Wallet::updateOrCreate(
                ['user_id' => $user->id],
                ['balance' => $userData['initial_balance']]
            );

            return ['user' => $user, 'wallet' => $wallet];
        })->filter();

        // Définir différentes configurations de tontines
        $tontineConfigs = [
            [
                'amount' => 100.00,
                'frequency' => 'quotidienne',
                'duration' => 3,
            ],
            // [
            //     'amount' => 200.00,
            //     'frequency' => 'quotidienne',
            //     'duration' => 4,
            // ],
            // [
            //     'amount' => 150.00,
            //     'frequency' => 'quotidienne',
            //     'duration' => 5,
            // ]
        ];

        // Créer les tontines pour chaque utilisateur
        $allTontines = collect();

        foreach ($users as $userData) {
            foreach ($tontineConfigs as $config) {
                $tontine = $this->createTontineForUser($userData['user'], $userData['wallet'], $config);
                if ($tontine) {
                    $allTontines->push($tontine);
                }
            }
        }

        // Simuler le passage du temps et le traitement des paiements
        $maxDuration = collect($tontineConfigs)->max('duration');

        for ($day = 0; $day < $maxDuration; $day++) {
            $currentDate = $startTestDate->copy()->addDays($day);
            Carbon::setTestNow($currentDate);

            Log::info("Traitement des paiements pour le jour : " . $currentDate->toDateString());

            DB::transaction(function () use ($currentDate, $allTontines) {
                foreach ($allTontines as $tontine) {
                    // Convertir next_payment_date en objet Carbon pour la comparaison
                    $nextPaymentDate = Carbon::parse($tontine->next_payment_date);
                    if ($nextPaymentDate->toDateString() === $currentDate->toDateString()) {
                        $this->processTontinePaiements($tontine);
                    }
                }
            });
        }

        // Vérifications
        foreach ($allTontines as $tontine) {
            $this->validateTontinePayments($tontine);
        }
    }

    private function createTontineForUser(User $user, Wallet $wallet, array $config)
    {
        try {
            DB::beginTransaction();

            $startDate = Carbon::now();
            $endDate = $startDate->copy()->addDays($config['duration'] - 1);

            // Créer un nouveau gelement spécifique pour cette tontine
            $gelementReference = $this->generateUniqueReference();
            $gelement = gelement::create([
                'reference_id' => $gelementReference,
                'id_wallet' => $wallet->id,
                'amount' => $config['amount'],
                'status' => 'pending' // Ajout d'un statut initial
            ]);

            // Créer la tontine avec référence au gelement
            $tontine = Tontines::create([
                'date_debut' => $startDate->toDateString(),
                'montant_cotisation' => $config['amount'],
                'frequence' => $config['frequency'],
                'date_fin' => $endDate->toDateString(),
                'next_payment_date' => $startDate->toDateString(),
                'gain_potentiel' => $config['amount'] * $config['duration'],
                'nombre_cotisations' => $config['duration'],
                'frais_gestion' => ($config['amount'] * $config['duration']) * 0.05,
                'user_id' => $user->id,
                'statut' => '1st',
                'gelement_reference' => $gelementReference // Ajout de la référence du gelement
            ]);

            TontineUser::create(['tontine_id' => $tontine->id, 'user_id' => $user->id]);

            $this->transactionService->createTransaction(
                $user->id,
                $user->id,
                'Gele',
                $config['amount'],
                $this->generateIntegerReference(),
                "Gelement pour tontine {$tontine->id}",
                'COC'
            );

            DB::commit();
            return $tontine;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Erreur lors de la création de la tontine: " . $e->getMessage());
            return null;
        }
    }

    private function processPayment(User $user, Tontines $tontine)
    {


        $userWallet = Wallet::where('user_id', $user->id)->first();
        if (!$userWallet) {
            Log::error("Wallet introuvable pour l'utilisateur", ['user_id' => $user->id]);
            return;
        }

        try {
            DB::beginTransaction();

            if ($tontine->statut === '1st') {
                // Récupérer le gelement spécifique à cette tontine
                $gelement = gelement::where('reference_id', $tontine->gelement_reference)
                    ->where('id_wallet', $userWallet->id)
                    ->where('status', 'pending')
                    ->first();

                if (!$gelement || $gelement->amount < $tontine->montant_cotisation) {
                    throw new \Exception("Gelement insuffisant ou invalide");
                }

                $gelement->amount -= $tontine->montant_cotisation;
                $gelement->status = 'OK';
                $gelement->save();

                $tontine->update(['statut' => 'active']);
            } else {
                // Pour les paiements réguliers, vérifier le solde disponible
                // en tenant compte des autres tontines actives
                $montantTotalEngagé = $this->calculateMontantEngagé($user->id);
                $soldeDisponible = $userWallet->balance - $montantTotalEngagé;


                if ($soldeDisponible < $tontine->montant_cotisation) {
                    throw new \Exception("Solde insuffisant après engagements");
                }

                $userWallet->balance -= $tontine->montant_cotisation;
                $userWallet->save();
            }

            // Créer la transaction et la cotisation
            $reference = $this->generateIntegerReference();

            $this->transactionService->createTransaction(
                $user->id,
                $user->id,
                'Débit',
                $tontine->montant_cotisation,
                $reference,
                'Paiement de cotisation',
                'COC'
            );

            Cotisation::create([
                'user_id' => $user->id,
                'tontine_id' => $tontine->id,
                'montant' => $tontine->montant_cotisation,
                'statut' => 'payé'
            ]);

            Notification::send($user, new tontinesNotification([
                'title' => 'Paiement effectué avec succès',
                'description' => 'Cliquez pour voir les détails.'
            ]));

            DB::commit();
            Log::info("Paiement traité avec succès");
        } catch (\Exception $e) {
            DB::rollBack();

            $this->handlePaymentFailure($user, $tontine);
        }
    }

    private function calculateMontantEngagé($userId)
    {
        // Calculer le montant total engagé dans toutes les tontines actives
        $tontinesActives = Tontines::where('user_id', $userId)
            ->where('statut', 'active')
            ->where('date_fin', '>=', now())
            ->get();

        $montantEngagé = 0;
        foreach ($tontinesActives as $tontine) {
            $montantEngagé += $tontine->montant_cotisation;
        }

        return $montantEngagé;
    }

    private function handlePaymentFailure(User $user, Tontines $tontine)
    {
        $cotisation = Cotisation::create([
            'user_id' => $user->id,
            'tontine_id' => $tontine->id,
            'montant' => $tontine->montant_cotisation,
            'statut' => 'échec'
        ]);

        EchecPaiement::create([
            'user_id' => $user->id,
            'cotisation_id' => $cotisation->id,
            'montant_du' => $tontine->montant_cotisation
        ]);

        Notification::send($user, new tontinesNotification([
            'title' => 'Échec de paiement',
            'description' => 'Cliquez pour voir les détails.'
        ]));
    }
    private function processTontinePaiements(Tontines $tontine)
    {
        $users = $tontine->users;

        foreach ($users as $user) {
            $this->processPayment($user, $tontine);
        }

        // Convertir next_payment_date en Carbon pour le calcul
        $currentPaymentDate = Carbon::parse($tontine->next_payment_date);

        // Calculer la prochaine date de paiement
        $nextPaymentDate = match ($tontine->frequence) {
            'quotidienne' => $currentPaymentDate->addDay(),
            'hebdomadaire' => $currentPaymentDate->addWeek(),
            'mensuelle' => $currentPaymentDate->addMonth(),
            default => throw new \Exception("Fréquence inconnue")
        };

        // Convertir date_fin en Carbon pour la comparaison
        $dateFin = Carbon::parse($tontine->date_fin);
        if ($nextPaymentDate->lte($dateFin)) {
            $tontine->update(['next_payment_date' => $nextPaymentDate->toDateString()]);
        }
    }



    private function validateTontinePayments(Tontines $tontine)
    {
        // Vérifier le nombre total de cotisations
        $totalCotisations = Cotisation::where('tontine_id', $tontine->id)
            ->where('statut', 'payé')
            ->count();

        // Vérifier le montant total collecté
        $totalCollecte = Cotisation::where('tontine_id', $tontine->id)
            ->where('statut', 'payé')
            ->sum('montant');

        // Vérifier les échecs de paiement
        $echecsPaiement = EchecPaiement::whereHas('cotisation', function ($query) use ($tontine) {
            $query->where('tontine_id', $tontine->id);
        })->count();

        // Assertions
        $this->assertEquals($tontine->nombre_cotisations, $totalCotisations, "Le nombre de cotisations ne correspond pas pour la tontine {$tontine->id}");
        $this->assertEquals($tontine->gain_potentiel, $totalCollecte, "Le montant total collecté ne correspond pas pour la tontine {$tontine->id}");
    }
}

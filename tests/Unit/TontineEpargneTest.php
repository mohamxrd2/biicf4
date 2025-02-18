<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\TontineEpargne;
use App\Jobs\ProcessPayment;
use App\Models\Tontines;
use App\Models\User;
use App\Services\RecuperationTimer;
use App\Services\TimeSync\TimeSyncService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Mockery;
use Tests\TestCase;
use Illuminate\Support\Facades\DB;

class TontineEpargneTest extends TestCase
{
    use RefreshDatabase;

    protected $recuperationTimerMock;
    protected $timeSyncServiceMock;

    public function setUp(): void
    {
        parent::setUp();
        // Utilisation correcte de Mockery pour créer des mocks
        $this->recuperationTimerMock = Mockery::mock(RecuperationTimer::class);
        $this->timeSyncServiceMock = Mockery::mock(TimeSyncService::class);
        $this->app->instance(RecuperationTimer::class, $this->recuperationTimerMock);
    }

    public function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test si la commande s'arrête quand l'heure ne peut pas être synchronisée
     */
    public function testCommandStopsWhenSyncFails()
    {
        // Configurer le mock pour retourner une synchronisation échouée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(false);

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $result = $command->handle();

        // Vérifier que la commande s'est arrêtée
        $this->assertNull($result);
    }

    /**
     * Test le traitement normal des paiements de tontine
     */
    public function testNormalPaymentProcessing()
    {
        // Configurer le mock pour l'heure synchronisée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(['timestamp' => now()->timestamp]);

        // Créer utilisateurs et tontines de test
        $user1 = User::find(121);
        $user2 = User::find(122);

        $tontine = Tontines::factory()->create([
            'nom' => 'Tontine Test',
            'montant_cotisation' => 100,
            'frequence' => 'mensuelle',
            'next_payment_date' => now()->subDay(), // Le paiement est dû
            'date_fin' => now()->addMonths(6)
        ]);

        // Lier les utilisateurs à la tontine
        $tontine->users()->attach([$user1->id, $user2->id]);

        // Intercepter les dispatch de jobs
        Queue::fake();

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $command->handle();

        // Vérifier que les jobs ont été dispatchés pour chaque utilisateur
        Queue::assertPushedOn('default', ProcessPayment::class, function ($job) use ($user1, $tontine) {
            return $job->user->id === $user1->id && $job->tontine->id === $tontine->id;
        });

        Queue::assertPushedOn('default', ProcessPayment::class, function ($job) use ($user2, $tontine) {
            return $job->user->id === $user2->id && $job->tontine->id === $tontine->id;
        });

        // Recharger la tontine depuis la base de données
        $tontine->refresh();

        // Vérifier que la prochaine date de paiement a été mise à jour correctement
        $expectedNextPaymentDate = Carbon::parse($tontine->next_payment_date)
            ->subDay() // Revenir à la date originale avant le test
            ->addMonth(); // Ajouter un mois car la fréquence est mensuelle

        $this->assertEquals(
            $expectedNextPaymentDate->format('Y-m-d'),
            Carbon::parse($tontine->next_payment_date)->format('Y-m-d')
        );
    }

    /**
     * Test qu'une tontine sans utilisateurs n'exécute pas de paiements
     */
    public function testTontineWithoutUsers()
    {
        // Configurer le mock pour l'heure synchronisée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(['timestamp' => now()->timestamp]);

        // Créer une tontine sans utilisateurs
        $tontine = Tontines::factory()->create([
            'nom' => 'Tontine Sans Utilisateurs',
            'montant_cotisation' => 100,
            'frequence' => 'mensuelle',
            'next_payment_date' => now()->subDay(),
            'date_fin' => now()->addMonths(6)
        ]);

        // Intercepter les dispatch de jobs
        Queue::fake();

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $command->handle();

        // Vérifier qu'aucun job n'a été dispatché
        Queue::assertNothingPushed();

        // Vérifier que la prochaine date de paiement a quand même été mise à jour
        $tontine->refresh();
        $expectedNextPaymentDate = Carbon::parse($tontine->next_payment_date)
            ->subDay() // Revenir à la date originale avant le test
            ->addMonth();

        $this->assertEquals(
            $expectedNextPaymentDate->format('Y-m-d'),
            Carbon::parse($tontine->next_payment_date)->format('Y-m-d')
        );
    }

    /**
     * Test le comportement de la tontine à sa date finale
     */
    public function testTontineEndDate()
    {
        // Configurer le mock pour l'heure synchronisée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(['timestamp' => now()->timestamp]);

        // Créer utilisateur et tontine de test près de la fin
        $user = User::factory()->create();
        $tontine = Tontines::factory()->create([
            'nom' => 'Tontine Fin',
            'montant_cotisation' => 100,
            'frequence' => 'mensuelle',
            'next_payment_date' => now()->subDay(),
            'date_fin' => now()->addDays(15) // La fin arrive bientôt
        ]);

        // Lier l'utilisateur à la tontine
        $tontine->users()->attach($user->id);

        // Intercepter les dispatch de jobs
        Queue::fake();

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $command->handle();

        // Vérifier que le job a été dispatché
        Queue::assertPushed(ProcessPayment::class);

        // Recharger la tontine depuis la base de données
        $tontine->refresh();

        // Exécuter à nouveau la commande (la prochaine date de paiement devrait maintenant dépasser date_fin)
        $command->handle();

        // Vérifier qu'aucun nouveau job n'a été dispatché (un seul dans total)
        Queue::assertPushedTimes(ProcessPayment::class, 1);
    }

    /**
     * Test le comportement avec différentes fréquences de paiement
     *
     * @dataProvider frequencyProvider
     */
    public function testDifferentFrequencies($frequency, $addMethod)
    {
        // Configurer le mock pour l'heure synchronisée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(['timestamp' => now()->timestamp]);

        // Créer une tontine avec la fréquence spécifiée
        $tontine = Tontines::factory()->create([
            'nom' => "Tontine {$frequency}",
            'montant_cotisation' => 100,
            'frequence' => $frequency,
            'next_payment_date' => now()->subDay(),
            'date_fin' => now()->addYear()
        ]);

        // Créer un utilisateur et l'attacher à la tontine
        $user = User::factory()->create();
        $tontine->users()->attach($user->id);

        // Intercepter les dispatch de jobs
        Queue::fake();

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $command->handle();

        // Vérifier que le job a été dispatché
        Queue::assertPushed(ProcessPayment::class);

        // Recharger la tontine depuis la base de données
        $tontine->refresh();

        // Calculer la date attendue en fonction de la fréquence
        $expectedNextPaymentDate = Carbon::parse($tontine->next_payment_date)
            ->subDay() // Revenir à la date originale avant le test
            ->$addMethod();

        $this->assertEquals(
            $expectedNextPaymentDate->format('Y-m-d'),
            Carbon::parse($tontine->next_payment_date)->format('Y-m-d')
        );
    }

    /**
     * Fournisseur de données pour le test des fréquences
     */
    public function frequencyProvider()
    {
        return [
            ['quotidienne', 'addDay'],
            ['hebdomadaire', 'addWeek'],
            ['mensuelle', 'addMonth'],
        ];
    }

    /**
     * Test la gestion des erreurs lors du traitement des paiements
     */
    public function testErrorHandlingDuringProcessing()
    {
        // Configurer le mock pour l'heure synchronisée
        $this->app->instance(TimeSyncService::class, $this->timeSyncServiceMock);
        $this->timeSyncServiceMock->shouldReceive('getSynchronizedTime')
            ->once()
            ->andReturn(['timestamp' => now()->timestamp]);

        // Créer une tontine qui va générer une erreur
        $tontine = Tontines::factory()->create([
            'nom' => 'Tontine Erreur',
            'montant_cotisation' => 100,
            'frequence' => 'inconnu', // Fréquence qui causera une erreur
            'next_payment_date' => now()->subDay(),
            'date_fin' => now()->addMonths(6)
        ]);

        // Créer un utilisateur et l'attacher à la tontine
        $user = User::factory()->create();
        $tontine->users()->attach($user->id);

        // Utiliser Mockery pour les appels DB (façon correcte)
        DB::shouldReceive('beginTransaction')->once();
        DB::shouldReceive('commit')->never();
        DB::shouldReceive('rollBack')->once();

        // Intercepter les dispatch de jobs
        Queue::fake();

        // Exécuter la commande
        $command = $this->app->make(TontineEpargne::class);
        $command->handle();

        // Vérifier qu'aucun job n'a été dispatché à cause de l'erreur
        Queue::assertNothingPushed();
    }
}

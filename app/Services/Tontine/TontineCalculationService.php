<?php

namespace App\Services\Tontine;

use Carbon\Carbon;

class TontineCalculationService
{
    public function calculatePotentialGain(float $amount, int $duration): array
    {
        $nbre_depot = $duration;
        $montant_total = $amount * $nbre_depot;
        $frais_gestion = $montant_total / 30;
        $gain_potentiel = $montant_total - $frais_gestion;

        return [
            'montant_total' => $montant_total,
            'frais_gestion' => $frais_gestion,
            'gain_potentiel' => $gain_potentiel,
        ];
    }

    public function calculateEndDate(Carbon $startDate, string $frequency, int $duration): Carbon
    {
        return match ($frequency) {
            'quotidienne' => $startDate->copy()->addDays($duration),
            'hebdomadaire' => $startDate->copy()->addWeeks($duration),
            'mensuelle' => $startDate->copy()->addMonths($duration),
            default => $startDate->copy()
        };
    }

    public function calculateNextPaymentDate(Carbon $startDate, string $frequency): Carbon
    {
        return match ($frequency) {
            'quotidienne' => $startDate->copy(),
            'hebdomadaire' => $startDate->copy()->addWeek(),
            'mensuelle' => $startDate->copy()->addMonth(),
        };
    }
}

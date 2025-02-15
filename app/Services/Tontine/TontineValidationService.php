<?php
// app/Services/Tontine/TontineCalculationService.php
namespace App\Services\Tontine;

class TontineValidationService
{
    public function validateTontine(array $data): array
    {
        $errors = [
            'amount' => '',
            'frequency' => '',
            'duration' => ''
        ];

        $minDuration = $this->getMinDuration($data['frequency']);

        if (empty($data['amount'])) {
            $errors['amount'] = 'Le montant est obligatoire.';
        } elseif (!is_numeric($data['amount'])) {
            $errors['amount'] = 'Le montant doit être un nombre.';
        } elseif ($data['amount'] < 1000) {
            $errors['amount'] = 'Le montant minimum est de 1000 FCFA.';
        }

        if (empty($data['frequency'])) {
            $errors['frequency'] = 'Veuillez sélectionner une fréquence.';
        }

        if (empty($data['duration'])) {
            $errors['duration'] = 'Veuillez entrer une durée.';
        } elseif ($data['duration'] < $minDuration) {
            $errors['duration'] = "La durée minimale pour {$data['frequency']} est de $minDuration.";
        }

        return $errors;
    }

    private function getMinDuration(string $frequency): int
    {
        return match ($frequency) {
            'quotidienne' => 30,
            'hebdomadaire' => 4,
            'mensuelle' => 1,
            default => 1,
        };
    }
}

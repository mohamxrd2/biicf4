<?php

namespace Database\Factories;

use App\Models\Consommation;
use Illuminate\Database\Eloquent\Factories\Factory;

class ConsommationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name,
            'type' => $this->faker->randomElement(['produits', 'services']),
            'conditionnement' => $this->faker->words(4, true), // Limite de 4 mots
            'format' => $this->faker->randomElement(['Format A', 'Format B', 'Format C']),
            'qte' => $this->faker->numberBetween(1, 100),
            'prix' => $this->faker->randomFloat(2, 1, 1000),
            'frqce_cons' => $this->faker->randomElement(['Quotidienne', 'Hebdomadaire', 'Mensuelle']),
            'jourAch_cons' => $this->faker->dayOfWeek,
            'qualif_serv' => $this->faker->randomElement(['Qualif A', 'Qualif B', 'Qualif C']),
            'specialité' => $this->faker->randomElement(['Specialite A', 'Specialite B', 'Specialite C']),
            'description' => $this->faker->words(4, true), // Limite de 4 mots
            'zoneAct' => $this->faker->words(4, true), // Limite de 4 mots
            'villeCons' => $this->faker->city,
        ];
    }
}

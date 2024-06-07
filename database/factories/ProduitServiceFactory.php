<?php

namespace Database\Factories;
use App\Models\ProduitService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProduitService>
 */
class ProduitServiceFactory extends Factory
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
            'type' => $this->faker->randomElement(['services', 'produits']),
            'condProd' => $this->faker->words(5, true), // Limite de 5 mots
            'formatProd' => $this->faker->words(5, true), // Limite de 5 mots
            'qteProd_min' => $this->faker->randomNumber(),
            'qteProd_max' => $this->faker->randomNumber(),
            'prix' => $this->faker->randomFloat(2, 0, 1000),
            'LivreCapProd' => $this->faker->boolean,
            'desrip' => $this->faker->sentence(10),
            'qalifServ' => $this->faker->randomNumber(),
            'sepServ' => $this->faker->words(5, true), // Limite de 5 mots
            'qteServ' => $this->faker->randomNumber(),
            'zonecoServ' => $this->faker->word,
            'villeServ' => $this->faker->city,
            'comnServ' => $this->faker->words(5, true), // Limite de 5 mots
            'typeProdServ' => $this->faker->word,
        ];

    }
}

<?php

namespace Database\Factories;
use App\Models\CategorieProduits_Servives;
use App\Models\ProduitService;
use App\Models\User;
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
            'type' => $this->faker->randomElement(['Produit', 'Service']),
            'reference' => $this->faker->word(7),
            'name' => $this->faker->word,
            'condProd' => $this->faker->optional()->word,
            'formatProd' => $this->faker->optional()->word,
            'Particularite' => $this->faker->optional()->word,
            'origine' => $this->faker->optional()->word,
            'qteProd_min' => $this->faker->optional()->numberBetween(1, 100),
            'qteProd_max' => $this->faker->optional()->numberBetween(101, 1000),
            'specification' => $this->faker->optional()->text,
            'prix' => $this->faker->randomFloat(2, 10, 1000),
            'qalifServ' => $this->faker->optional()->word,
            'sepServ' => $this->faker->optional()->word,
            'description' => $this->faker->optional()->text,
            'quantite' => $this->faker->optional()->numberBetween(1, 100),
            'continent' => $this->faker->word,
            'sous_region' => $this->faker->word,
            'pays' => $this->faker->word,
            'zonecoServ' => $this->faker->word,
            'villeServ' => $this->faker->word,
            'comnServ' => $this->faker->word,
            'user_id' => User::factory(),
            // 'categorie_id' => CategorieProduits_Servives::factory(), // Assuming you have a Categorie model
        ];

    }
}

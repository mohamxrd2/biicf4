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
            'type' => $this->faker->randomElement(['Produit']),
            'reference' => 'REF-JGKCPG',
            'name' => 'cigare',
            'condProd' => 'sac',
            'formatProd' => 'long',
            'Particularite' => 'marron pur',
            'origine' => 'Importé',
            'qteProd_min' => $this->faker->optional()->numberBetween(1, 100),
            'qteProd_max' => $this->faker->optional()->numberBetween(101, 1000),
            'specification' => 'force longue',
            'prix' => $this->faker->randomFloat(2, 10, 1000),
            'continent' => 'Afrique',
            'sous_region' => 'Afrique de l\'Ouest',
            'pays' => 'Ivory Coast',
            'zonecoServ' => 'Lagunes',
            'villeServ' => 'Abidjan',
            'comnServ' => 'Cocody',
            'user_id' => User::factory(),
            // 'categorie_id' => CategorieProduits_Servives::factory(), // Uncomment if using a Categorie model
        ];

        // 'qalifServ' => $this->faker->optional()->word,
        // 'sepServ' => $this->faker->optional()->word,
        // 'description' => $this->faker->optional()->text,
        // 'quantite' => $this->faker->optional()->numberBetween(1, 100),
    }
}

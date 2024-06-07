<?php

namespace Database\Factories;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash; // Import de la classe Hash

class AdminFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name, // Utilisation de $this->faker ou $faker
            'username' => $this->faker->userName,
            'password' => Hash::make('admin'), // Hashage du mot de passe
            'phonenumber' => $this->faker->phoneNumber,
            'admin_type' => 'agent',
            // Autres champs si nécessaire
        ];
    }
}

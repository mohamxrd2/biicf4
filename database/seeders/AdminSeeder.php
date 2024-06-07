<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Données de l'administrateur à insérer dans la table 'admins'
        $adminData = [
            'name' => 'ahondjo',
            'username' => 'admin',
            'password' => Hash::make('admin'), // Hashage du mot de passe
            'phonenumber' => '0576507639',

            // Autres champs si nécessaire
        ];

        // // Création d'un nouvel administrateur dans la table 'admins'
        Admin::create($adminData);



        // Vous pouvez ajouter plus d'administrateurs si nécessaire
    }
}

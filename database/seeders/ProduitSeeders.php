<?php

namespace Database\Seeders;

use App\Models\ProduitService;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProduitSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProduitService::factory(5)->create();

    }
}

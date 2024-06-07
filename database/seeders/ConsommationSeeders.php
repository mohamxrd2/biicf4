<?php

namespace Database\Seeders;

use App\Models\Consommation;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConsommationSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Consommation::factory(20)->create();
    }
}

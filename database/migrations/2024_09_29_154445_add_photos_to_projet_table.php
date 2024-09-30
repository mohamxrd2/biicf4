<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projet', function (Blueprint $table) {
            //
            $table->string('photo1')->nullable(); // Colonne pour la première photo
            $table->string('photo2')->nullable(); // Colonne pour la deuxième photo
            $table->string('photo3')->nullable(); // Colonne pour la troisième photo
            $table->string('photo4')->nullable(); // Colonne pour la quatrième photo
            $table->string('photo5')->nullable(); // Colonne pour la cinquième photo
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projet', function (Blueprint $table) {
            //
            $table->dropColumn(['photo1', 'photo2', 'photo3', 'photo4', 'photo5']); // Suppression des colonnes en cas de rollback
        });
    }
};

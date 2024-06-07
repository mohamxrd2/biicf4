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
        Schema::table('produit_services', function (Blueprint $table) {
            $table->string('statuts')->default('En attente'); // Ajout de l'attribut 'statuts' avec une valeur par défaut 'En attente'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produit_services', function (Blueprint $table) {
            $table->dropColumn('statuts'); // Suppression de l'attribut 'statuts' si la migration est annulée
        });
    }
};

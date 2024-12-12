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
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Modification du type de colonne `rib` en bigint
            $table->bigInteger('rib')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Si nécessaire, retour à l'ancien type (exemple : string)
            $table->integer('rib')->change();
        });
    }
};

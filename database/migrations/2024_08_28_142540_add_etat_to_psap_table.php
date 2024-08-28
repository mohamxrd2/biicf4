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
        Schema::table('psap', function (Blueprint $table) {
            $table->string('etat'); // Ajoute la colonne etat avec une valeur par défaut
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psap', function (Blueprint $table) {
            $table->dropColumn('etat'); // Supprime la colonne etat si la migration est annulée
        });
    }
};

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
            $table->string('bailleur')->nullable(); // Attribut 'bailleur' (nullable, type VARCHAR)
            $table->longText('id_investisseur')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projet', function (Blueprint $table) {
            //
            $table->dropColumn(['bailleur', 'id_investisseur']); // Supprimer les colonnes si on revient en arrière
        });
    }
};

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
        Schema::table('ajout_montant', function (Blueprint $table) {
            //
            $table->dropColumn('code_unique'); // Suppression de la colonne code_unique
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajout_montant', function (Blueprint $table) {
            //
            $table->string('code_unique');
        });
    }
};

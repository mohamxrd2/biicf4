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
        Schema::table('achat_direct', function (Blueprint $table) {
            $table->json('data_finance')->nullable()->after('prix'); // Remplace `colonne_existante` par la colonne après laquelle tu veux ajouter `data_finance`
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achat_direct', function (Blueprint $table) {
            $table->dropColumn('data_finance');
        });
    }
};

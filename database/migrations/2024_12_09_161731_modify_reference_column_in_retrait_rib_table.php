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
            // Modifier la colonne `reference` pour être un BIGINT(20)
            $table->bigInteger('reference', false, true)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Revert the column type back to INTEGER
            $table->integer('reference')->change();
        });
    }
};

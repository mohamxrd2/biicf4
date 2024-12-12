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
            // Modifier la colonne 'rib' pour qu'elle soit de type VARCHAR(25)
            $table->string('rib', 25)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Revenir à l'ancien type si nécessaire, ici on suppose qu'il était 'bigint'
            $table->bigInteger('rib')->change();
        });
    }
};

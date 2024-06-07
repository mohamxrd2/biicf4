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
        // Modifier la colonne isban pour avoir la valeur par défaut false
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('isban')->default(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revenir à la valeur par défaut true pour la colonne isban
        Schema::table('admins', function (Blueprint $table) {
            $table->boolean('isban')->default(true)->change();
        });
    }
};

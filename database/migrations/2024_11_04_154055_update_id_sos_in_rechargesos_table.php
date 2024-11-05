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
        Schema::table('rechargesos', function (Blueprint $table) {
            // Modifier le type de la colonne id_sos en string
            $table->string('id_sos')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rechargesos', function (Blueprint $table) {
            // Revenir au type initial si nécessaire
            $table->integer('id_sos')->change();
        });
    }
};

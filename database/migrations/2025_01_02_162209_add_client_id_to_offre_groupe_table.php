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
        Schema::table('offre_groupe', function (Blueprint $table) {
            // Ajout de la colonne client_id
            $table->unsignedBigInteger('client_id')->nullable();

            // Ajout de la clé étrangère
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_groupe', function (Blueprint $table) {
            //
        });
    }
};

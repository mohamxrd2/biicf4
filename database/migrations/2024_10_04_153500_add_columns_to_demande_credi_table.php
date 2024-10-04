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
        Schema::table('demande_credi', function (Blueprint $table) {
            $table->string('demande_id')->nullable(); // Identifiant de la demande
            $table->string('objet_financement')->nullable(); // Objet du financement
            $table->unsignedBigInteger('id_investisseur')->nullable(); // ID de l'investisseur
            $table->foreign('id_investisseur')->references('id')->on('investisseurs'); // Clé étrangère
            $table->date('date_debut')->nullable(); // Date de début
            $table->date('date_fin')->nullable(); // Date de fin
            $table->time('heure_debut')->nullable(); // Heure de début
            $table->time('heure_fin')->nullable(); // Heure de fin
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_credi', function (Blueprint $table) {
            //
        });
    }
};

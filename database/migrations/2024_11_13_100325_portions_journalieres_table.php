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
        Schema::create('portions_journalieres', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_user'); // ID du crédit concerné
            $table->date('date_portion'); // Date de la portion journalière
            $table->decimal('portion_capital', 10, 2); // Portion du capital à rembourser pour la journée
            $table->decimal('portion_interet', 10, 2); // Portion d'intérêt à rembourser pour la journée
            $table->timestamps();

            // Clé étrangère pour relier à la table `credits`
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

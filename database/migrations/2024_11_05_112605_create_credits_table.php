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
        Schema::create('credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emprunteur_id')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (demandeur)

            $table->json('investisseurs'); // Données de l'investisseur en format JSON
            $table->decimal('montant', 10, 2); // Montant du crédit
            $table->decimal('taux_interet', 5, 2); // Taux d'intérêt en %
            $table->date('date_debut'); // Date de début du crédit
            $table->date('date_fin'); // Date de fin prévue du crédit
            $table->decimal('portion_journaliere', 10, 2); // Portion journalière à rembourser
            $table->string('statut')->default('pending'); // Statut du crédit (en_cours, terminé, annulé)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credits');
    }
};

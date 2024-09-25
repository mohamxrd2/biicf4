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
        Schema::create('projet', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2); // Montant du projet
            $table->decimal('taux', 5, 2); // Taux du projet
            $table->text('description'); // Description du projet
            $table->string('categorie'); // Catégorie du projet
            $table->enum('type_financement', ['direct', 'groupé', 'négocié']); // Type de financement
            $table->enum('statut', ['en attente', 'approuvé', 'rejeté']); // Statut du projet
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (demandeur)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projet');
    }
};

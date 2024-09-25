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
        Schema::create('demande_credi', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2); // Montant du crédit
            $table->decimal('taux', 5, 2); // Taux d'intérêt en pourcentage
            $table->string('type_financement'); // Type de financement
            $table->string('bailleur')->nullable(); // Bailleur de fonds, nullable
            $table->integer('duree'); // Durée du crédit en mois
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('demande_credi');
    }
};

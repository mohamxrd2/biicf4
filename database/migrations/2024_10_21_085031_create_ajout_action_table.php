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
        Schema::create('ajout_action', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 15, 2); // Montant à ajouter
            $table->foreignId('id_invest')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (investisseur)
            $table->foreignId('id_emp')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (emprunteur)
            $table->foreignId('id_projet')->constrained('projet')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajout_action', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::create('cedd', function (Blueprint $table) {
            $table->id();
            $table->decimal('Solde', 15, 2); // Solde
            $table->string('Numero_compte')->unique(); // Numéro de compte
            $table->date('Date_Creation');
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (demandeur)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cedd');
    }
};

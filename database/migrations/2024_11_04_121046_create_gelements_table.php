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
        Schema::create('gelements', function (Blueprint $table) {
            $table->id(); // Identifiant unique de la transaction de gel
            $table->unsignedBigInteger('id_wallet');
            $table->decimal('amount', 10, 2); // Montant bloqué
            $table->unsignedBigInteger('reference_id');
            $table->string('status', 20)->default('Pending'); // Statut de la transaction (Pending, Released, Refunded)
            $table->timestamps(); // Date de création et de dernière mise à jour

            // Définir les clés étrangères
            $table->foreign('id_wallet')->references('id')->on('wallets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gelements');
    }
};

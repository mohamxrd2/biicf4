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
        Schema::create('retrait_rib', function (Blueprint $table) {
            $table->id(); // ID auto-incrémenté
            $table->integer('rib'); // Le champ RIB (numéro de compte bancaire)
            $table->decimal('amount', 15, 2); // Montant du retrait
            $table->unsignedBigInteger('id_user'); // Clé étrangère vers la table user
            $table->integer('reference'); // Référence pour le retrait
            $table->timestamps(); // Les colonnes created_at et updated_at

            // Définition de la clé étrangère
            $table->foreign('id_user')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('retrait_rib');
    }
};

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
        Schema::create('rechargesos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('userdem'); // Clé étrangère vers user
            $table->unsignedBigInteger('userinvest'); // Clé étrangère vers user
            $table->decimal('montant', 15, 2); // Montant de la recharge
            $table->decimal('roi', 15, 2); // ROI
            $table->string('operator'); // Opérateur de recharge
            $table->string('phone'); // Numéro de téléphone
            $table->string('statut')->default('en attente'); // Statut de la demande
            $table->integer('id_sos', );
            $table->timestamps();

            // Définition des clés étrangères
            $table->foreign('userdem')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('userinvest')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rechargesos');
    }
};

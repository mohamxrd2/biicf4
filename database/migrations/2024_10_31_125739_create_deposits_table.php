<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('deposits', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 20, 2); // Montant du dépôt avec précision
            $table->string('recu'); // Reçu du dépôt (URL ou chemin)
            $table->unsignedBigInteger('user_id'); // ID de l'utilisateur
            $table->string('statut')->default('en attente'); // Statut avec valeur par défaut
            $table->timestamps();

            // Définition de la clé étrangère vers la table users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('deposits');
    }
};

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
        Schema::table('coi', function (Blueprint $table) {
            // Ajout de la colonne 'type_compte'
            $table->string('type_compte')->after('Numero_compte'); // Ajout du type de compte

            // Suppression de la clé étrangère 'id_user' et de la colonne associée
            $table->dropForeign(['id_user']);
            $table->dropColumn('id_user');

            // Suppression de la colonne 'Numero_compte' si nécessaire
            $table->dropColumn('Numero_compte');  // Retire Numero_compte (non une clé étrangère)

            // Ajout de la clé étrangère 'id_wallet' pour la table 'wallet'
            $table->foreignId('id_wallet')->constrained('wallets')->onDelete('cascade'); // Ajoute la relation avec wallet
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coi', function (Blueprint $table) {
            // Suppression de la clé étrangère 'id_wallet'
            $table->dropForeign(['id_wallet']);
            $table->dropColumn('id_wallet');

            // Réintroduction de 'id_user' et sa clé étrangère
            $table->foreignId('id_user')->constrained('users')->onDelete('cascade');

            // Réintroduction de 'Numero_compte' si nécessaire
            $table->string('Numero_compte')->unique();

            // Suppression de la colonne 'type_compte'
            $table->dropColumn('type_compte');
        });
    }
};

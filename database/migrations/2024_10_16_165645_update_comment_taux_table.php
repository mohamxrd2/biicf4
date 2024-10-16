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
        Schema::table('comment_taux', function (Blueprint $table) {
            // Supprimer la colonne 'code_unique' si elle existe
            $table->dropColumn('code_unique');

            // Ajouter la colonne 'id_projet' comme clé étrangère
            $table->unsignedBigInteger('id_projet')->nullable()->after('id_emp');
            $table->foreign('id_projet')->references('id')->on('projets')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_taux', function (Blueprint $table) {
            // Reajouter la colonne 'code_unique'
            $table->string('code_unique')->nullable(); // Adapté en fonction de la définition précédente

            // Supprimer la contrainte de clé étrangère
            $table->dropForeign(['id_projet']);

            // Supprimer la colonne 'id_projet'
            $table->dropColumn('id_projet');
        });
    }
};

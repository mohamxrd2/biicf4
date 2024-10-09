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
        Schema::table('demande_credi', function (Blueprint $table) {
            if (!Schema::hasColumn('demande_credi', 'demande_id')) {
                $table->string('demande_id')->nullable(); // Identifiant de la demande
            }
            if (!Schema::hasColumn('demande_credi', 'objet_financement')) {
                $table->string('objet_financement')->nullable(); // Objet du financement
            }
            if (!Schema::hasColumn('demande_credi', 'id_investisseur')) {
                $table->unsignedBigInteger('id_investisseur')->nullable(); // ID de l'investisseur
                $table->foreign('id_investisseur')->references('id')->on('investisseurs'); // Clé étrangère
            }
            if (!Schema::hasColumn('demande_credi', 'date_debut')) {
                $table->date('date_debut')->nullable(); // Date de début
            }
            if (!Schema::hasColumn('demande_credi', 'date_fin')) {
                $table->date('date_fin')->nullable(); // Date de fin
            }
            if (!Schema::hasColumn('demande_credi', 'heure_debut')) {
                $table->time('heure_debut')->nullable(); // Heure de début
            }
            if (!Schema::hasColumn('demande_credi', 'heure_fin')) {
                $table->time('heure_fin')->nullable(); // Heure de fin
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('demande_credi', function (Blueprint $table) {
            if (Schema::hasColumn('demande_credi', 'demande_id')) {
                $table->dropColumn('demande_id');
            }
            if (Schema::hasColumn('demande_credi', 'objet_financement')) {
                $table->dropColumn('objet_financement');
            }
            if (Schema::hasColumn('demande_credi', 'id_investisseur')) {
                $table->dropForeign(['id_investisseur']);
                $table->dropColumn('id_investisseur');
            }
            if (Schema::hasColumn('demande_credi', 'date_debut')) {
                $table->dropColumn('date_debut');
            }
            if (Schema::hasColumn('demande_credi', 'date_fin')) {
                $table->dropColumn('date_fin');
            }
            if (Schema::hasColumn('demande_credi', 'heure_debut')) {
                $table->dropColumn('heure_debut');
            }
            if (Schema::hasColumn('demande_credi', 'heure_fin')) {
                $table->dropColumn('heure_fin');
            }
        });
    }
};


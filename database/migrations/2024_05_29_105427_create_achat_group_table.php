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
        Schema::table('achat_group', function (Blueprint $table) {
            // Vérifiez d'abord si les colonnes existent avant de les ajouter
            if (!Schema::hasColumn('achat_group', 'photoProd')) {
                $table->string('photoProd');
            }
            if (!Schema::hasColumn('achat_group', 'nameProd')) {
                $table->string('nameProd');
            }
            if (!Schema::hasColumn('achat_group', 'quantité')) {
                $table->integer('quantité');
            }
            if (!Schema::hasColumn('achat_group', 'montantTotal')) {
                $table->decimal('montantTotal', 10, 2);
            }
            if (!Schema::hasColumn('achat_group', 'localite')) {
                $table->string('localite');
            }
            if (!Schema::hasColumn('achat_group', 'reponse')) {
                $table->boolean('reponse');
            }
            if (!Schema::hasColumn('achat_group', 'userTrader')) {
                $table->unsignedBigInteger('userTrader');
            }
            if (!Schema::hasColumn('achat_group', 'userSender')) {
                $table->unsignedBigInteger('userSender');
            }
            if (!Schema::hasColumn('achat_group', 'idProd')) {
                $table->unsignedBigInteger('idProd');
            }
            // Les colonnes `created_at` et `updated_at` peuvent être ajoutées en utilisant timestamps() si elles n'existent pas
            if (!Schema::hasColumns('achat_group', ['created_at', 'updated_at'])) {
                $table->timestamps();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achat_group', function (Blueprint $table) {
            // Supprimer les colonnes si nécessaire
            $table->dropColumn(['photoProd', 'nameProd', 'quantité', 'montantTotal', 'localite', 'reponse', 'userTrader', 'userSender', 'idProd']);
        });
    }
};



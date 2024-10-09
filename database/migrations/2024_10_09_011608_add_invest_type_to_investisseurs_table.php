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
        Schema::table('investisseurs', function (Blueprint $table) {
            // Ajouter la colonne si elle n'existe pas
            if (!Schema::hasColumn('investisseurs', 'invest_type')) {
                $table->string('invest_type')->nullable()->after('tranche'); // Ajout de la colonne si elle n'existe pas
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investisseurs', function (Blueprint $table) {
            // Optionnel : tu peux choisir de supprimer la colonne dans le down() si nécessaire
            if (Schema::hasColumn('investisseurs', 'invest_type')) {
                $table->dropColumn('invest_type');
            }
        });
    }
};


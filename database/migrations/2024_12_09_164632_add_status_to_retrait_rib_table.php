<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Ajouter la colonne `status` de type string
            $table->string('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            // Supprimer la colonne `status`
            $table->dropColumn('status');
        });
    }
};

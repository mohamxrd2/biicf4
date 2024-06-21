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
        Schema::table('offre_groupe', function (Blueprint $table) {
            //
            $table->string('name')->after('id'); // Ajoute la colonne 'name' après la colonne 'id'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offre_groupe', function (Blueprint $table) {
            //
            $table->dropColumn('name'); 
        });
    }
};

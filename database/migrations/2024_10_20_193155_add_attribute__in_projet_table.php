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
        Schema::table('projet', function (Blueprint $table) {
            $table->integer('Portion_action'); // Ajout du type de compte
            $table->integer('Portion_obligt')->after('Portion_action'); // Ajout du type de compte
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projet', function (Blueprint $table) {
            //
        });
    }
};

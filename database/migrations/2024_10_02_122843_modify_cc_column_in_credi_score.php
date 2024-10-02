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
        Schema::table('credi_score', function (Blueprint $table) {
            $table->string('ccc')->change(); // Modifie la colonne 'cc' en varchar
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credi_score', function (Blueprint $table) {
            //
        });
    }
};

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
        Schema::table('portions_journalieres', function (Blueprint $table) {
            $table->unsignedBigInteger('id_projet_accord')->nullable(); // Lien vers le crédit, si applicable

            $table->foreign('id_projet_accord')->references('id')->on('projets_accordés')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

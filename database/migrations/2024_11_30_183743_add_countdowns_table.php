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
        Schema::table('countdowns', function (Blueprint $table) {
            $table->unsignedBigInteger('AppelOffreGrouper_id')->nullable();

            $table->foreign('AppelOffreGrouper_id')->references('id')->on('appeloffregrouper')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countdowns');
    }
};

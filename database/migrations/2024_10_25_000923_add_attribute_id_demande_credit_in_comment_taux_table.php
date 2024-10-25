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
            $table->foreignId(column: 'id_demande_credit')->constrained('demande_credi')->onDelete('cascade'); // Clé étrangère vers la table demande_credi
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('comment_taux', function (Blueprint $table) {
            //
        });
    }
};

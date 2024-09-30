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
        Schema::table('ajout_montant', function (Blueprint $table) {
            //
            $table->foreignId('id_projet')->constrained('projet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajout_montant', function (Blueprint $table) {
            //
            $table->dropForeign(['id_projet']);
            $table->dropColumn('id_projet');
        });
    }
};

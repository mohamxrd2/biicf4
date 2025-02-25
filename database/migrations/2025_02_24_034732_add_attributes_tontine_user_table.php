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
        Schema::table('tontine_user', function (Blueprint $table) {
            $table->foreignId('tontine_cefp_id')
                ->constrained('tontines_cefp') // Assure que la clé fait référence à la bonne table
                ->after('tontine_id') // Assure que la clé fait référence à la bonne table
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tontine_user', function (Blueprint $table) {
            $table->dropForeign(['tontine_cefp_id']);
            $table->dropColumn('tontine_cefp_id');
        });
    }
};

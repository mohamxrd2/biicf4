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
        Schema::table('achat_direct', function (Blueprint $table) {
            $table->string('type_achat')->nullable()->after('specificite');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achat_direct', function (Blueprint $table) {
            // Supprimer la colonne type_achat
            $table->dropColumn('type_achat');
        });
    }
};

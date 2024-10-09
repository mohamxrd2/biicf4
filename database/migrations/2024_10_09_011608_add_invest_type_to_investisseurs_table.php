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
        Schema::table('investisseurs', function (Blueprint $table) {
            $table->string('invest_type')->nullable()->after('tranche'); // Ajoutez la colonne invest_type
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('investisseurs', function (Blueprint $table) {
            //
        });
    }
};

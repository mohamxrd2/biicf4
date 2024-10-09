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
            $table->string('invest_type')->nullable()->change()->after('tranche'); // Exemple de modification

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

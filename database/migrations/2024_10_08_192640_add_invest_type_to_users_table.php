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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'invest_type')) {
                $table->string('invest_type')->nullable()->after('actor_type'); // Ajoutez la colonne invest_type
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'invest_type')) {
                $table->dropColumn('invest_type'); // Supprimez la colonne invest_type si elle existe
            }
        });
    }
};

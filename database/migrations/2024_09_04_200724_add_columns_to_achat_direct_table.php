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
            $table->time('timeStart')->nullable()->after('code_unique');
            $table->time('timeEnd')->nullable()->after('timeStart');
            $table->string('dayPeriod')->nullable()->after('date_tard');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('achat_direct', function (Blueprint $table) {
            //
        });
    }
};

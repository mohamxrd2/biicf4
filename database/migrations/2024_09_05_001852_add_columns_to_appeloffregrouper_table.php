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
        Schema::table('appeloffregrouper', function (Blueprint $table) {
            $table->time('timeStart')->nullable()->after('codeunique');
            $table->time('timeEnd')->nullable()->after('timeStart');
            $table->string('dayPeriod')->nullable()->after('timeEnd');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appeloffregrouper', function (Blueprint $table) {
            //
        });
    }
};

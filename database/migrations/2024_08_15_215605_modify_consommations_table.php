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
        Schema::table('consommations', function (Blueprint $table) {
            $table->string('continent')->nullable()->after('specialité');
            $table->string('Sous-Region')->nullable()->after('continent'); // Adjusted `after` clause
            $table->string('departe')->nullable()->after('Sous-Region'); // Adjusted `after` clause
            $table->string('commune')->nullable()->after('villeCons');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consommations', function (Blueprint $table) {
            //
        });
    }
};

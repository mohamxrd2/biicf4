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
            $table->string('continent')->nullable()->after('commerce');
            $table->string('sous_region')->nullable()->after('continent'); // Adjusted `after` clause
            $table->string('departe')->nullable()->after('country'); // Adjusted `after` clause
            $table->string('commune')->nullable()->after('departe');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};

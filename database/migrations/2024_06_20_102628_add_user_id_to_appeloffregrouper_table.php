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
            $table->string('user_id')->nullable()->after('id'); // Add the unique code column after the id column
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

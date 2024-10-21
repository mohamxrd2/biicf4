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
        Schema::table('ajout_action', function (Blueprint $table) {
            $table->string('nombreActions')->nullable()->after('id'); //nombreActions
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ajout_action', function (Blueprint $table) {
            //
        });
    }
};

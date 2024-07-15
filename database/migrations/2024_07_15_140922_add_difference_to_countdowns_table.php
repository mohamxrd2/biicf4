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
        Schema::table('countdowns', function (Blueprint $table) {
            $table->string('difference')->nullable()->after('notified'); // Ajoute une nouvelle colonne 'new_attribute'

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('countdowns', function (Blueprint $table) {
            //
        });
    }
};

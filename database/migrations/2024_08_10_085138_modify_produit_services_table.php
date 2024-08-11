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
        Schema::table('produit_services', function (Blueprint $table) {
            $table->string('specification2')->nullable()->after('specification');
            $table->string('specification3')->nullable()->after('specification');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produit_services', function (Blueprint $table) {
            //
        });
    }
};
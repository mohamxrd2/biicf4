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
        Schema::table('livraisons', function (Blueprint $table) {
            $table->string('vehicle2')->nullable()->after('vehicle');
            $table->string('vehicle3')->nullable()->after('vehicle2');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('livraisons', function (Blueprint $table) {
            //
        });
    }
};

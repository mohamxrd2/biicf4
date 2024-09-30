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
        Schema::table('projet', function (Blueprint $table) {
            //
            $table->text('description')->change(); // Changer le type de 'description' en text
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projet', function (Blueprint $table) {
            //
            $table->string('description', 255)->change(); // Revenir à string avec une limite de 255 caractères
        });
    }
};

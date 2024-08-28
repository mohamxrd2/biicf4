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
        Schema::create('psap', function (Blueprint $table) {
            $table->id();
            $table->string('experience')->nullable();
            $table->string('continent')->nullable();
            $table->string('sous_region')->nullable();
            $table->string('pays')->nullable();
            $table->string('depart')->nullable();
            $table->string('ville')->nullable();
            $table->string('localite')->nullable();
            $table->string('identity')->nullable();
            $table->string('permis')->nullable();
            $table->string('assurance')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psap');
    }
};

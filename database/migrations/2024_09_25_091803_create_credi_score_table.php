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
        Schema::create('credi_score', function (Blueprint $table) {
            $table->id();
            $table->integer('ccc'); // Cote de crédit client
            $table->foreignId('id_user')->constrained('user_promir')->onDelete('cascade'); // Clé étrangère vers la table user_promir
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credi_score');
    }
};

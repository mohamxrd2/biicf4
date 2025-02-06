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
        Schema::create('cotisations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('tontine_id')->constrained('tontines')->onDelete('cascade');
            $table->decimal('montant', 10, 2);
            $table->enum('statut', ['payé', 'en attente', 'échec'])->default('en attente');
            $table->timestamps();
        });
    }

    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotisations');
    }
};

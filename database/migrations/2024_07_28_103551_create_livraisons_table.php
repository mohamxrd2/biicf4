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
        Schema::create('livraisons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ajouter cette ligne pour la clé étrangère
            $table->string('experience');
            $table->string('license');
            $table->string('vehicle');
            $table->string('matricule');
            $table->string('availability');
            $table->json('zone');
            $table->text('comments')->nullable();
            $table->string('identity');
            $table->string('permis');
            $table->string('assurance');
            $table->string('etat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('livraisons');
    }
};

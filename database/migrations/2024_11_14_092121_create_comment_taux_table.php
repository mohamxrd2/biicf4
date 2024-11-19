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
        // Schema::create('comment_taux', function (Blueprint $table) {
        //     $table->id();
        //     $table->decimal('taux', 5, 2); // Taux
        //     $table->string('code_unique'); // Code unique
        //     $table->foreignId('id_invest')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (investisseur)
        //     $table->foreignId(column: 'id_emp')->constrained('users')->onDelete('cascade'); // Clé étrangère vers la table users (emprunteur)

        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment_taux');
    }
};

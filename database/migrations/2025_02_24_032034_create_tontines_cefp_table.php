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
        Schema::create('tontines_cefp', function (Blueprint $table) {
            $table->id();
            $table->string('gelement_reference')->unique();
            $table->date('date_debut'); // La tontine commence à cette date
            $table->decimal('montant_cotisation', 10, 2);
            $table->string('frequence'); // ex: "mensuel", "hebdomadaire"
            $table->date('next_payment_date')->nullable(); // Prochain paiement
            $table->decimal('gain_actuel', 10, 2)->nullable();
            $table->integer('nombre_cotisations')->nullable(); // Peut être NULL pour une tontine infinie
            $table->decimal('frais_gestion', 10, 2)->default(0);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('statut', ['active', 'inactive', '1st'])->default('1st');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tontines_cefp');
    }
};

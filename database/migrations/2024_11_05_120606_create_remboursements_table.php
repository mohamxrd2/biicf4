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
        Schema::create('remboursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_id'); // Lien vers le crédit
            $table->decimal('montant_capital', 10, 2); // Portion du capital remboursé
            $table->decimal('montant_interet', 10, 2); // Portion d'intérêt remboursée
            $table->date('date_remboursement'); // Date de remboursement
            $table->string('statut')->default('effectue'); // Statut du remboursement (effectue, echoue, en_attente)
            $table->timestamps();

            $table->foreign('credit_id')->references('id')->on('credits')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remboursements');
    }
};

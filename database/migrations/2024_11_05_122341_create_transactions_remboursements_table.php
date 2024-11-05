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
        Schema::create('transactions_remboursements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_id'); // Lien vers le crédit concerné
            $table->unsignedBigInteger('emprunteur_id'); // Lien vers le compte de l'emprunteur
            $table->unsignedBigInteger('investisseur_id'); // Lien vers le compte de l'investisseur
            $table->decimal('montant', 10, 2); // Montant de la transaction
            $table->decimal('interet', 10, 2); // Partie du montant correspondant à l'intérêt
            $table->date('date_transaction'); // Date de la transaction
            $table->string('statut')->default('effectue'); // Statut de la transaction (effectue, echoue)
            $table->timestamps();

            $table->foreign('credit_id')->references('id')->on('credits')->onDelete('cascade');
            $table->foreign('emprunteur_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('investisseur_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions_remboursements');
    }
};

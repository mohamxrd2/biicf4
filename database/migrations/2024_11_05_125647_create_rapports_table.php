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
        Schema::create('rapports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('credit_id')->nullable(); // Lien vers le crédit, si applicable
            $table->unsignedBigInteger('transaction_id')->nullable(); // Lien vers la transaction, si applicable
            $table->text('contenu'); // Contenu du rapport (formaté pour l'audit)
            $table->string('type'); // Type de rapport (transaction, clôture, conformité, etc.)
            $table->date('date_rapport'); // Date de création du rapport
            $table->timestamps();
            $table->foreign('credit_id')->references('id')->on('credits')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions_remboursements')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rapports');
    }
};

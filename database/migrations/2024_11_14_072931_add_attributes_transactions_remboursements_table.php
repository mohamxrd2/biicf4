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
        Schema::table('transactions_remboursements', function (Blueprint $table) {

            $table->unsignedBigInteger('creditGrp_id')->after('credit_id'); // Lien vers le crédit concerné

            $table->foreign('creditGrp_id')->references('id')->on('credits_groupés')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

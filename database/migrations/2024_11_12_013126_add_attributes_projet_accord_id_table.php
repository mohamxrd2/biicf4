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
            $table->unsignedBigInteger('projet_accord_id')->nullable()->after('credit_id');

            $table->foreign('projet_accord_id')->references('id')->on('projets_accordés')->onDelete('cascade');
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

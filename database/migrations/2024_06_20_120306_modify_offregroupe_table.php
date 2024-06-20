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
        Schema::table('appeloffregrouper', function (Blueprint $table) {
            // Rendre toutes les colonnes nullable
            $table->string('productName')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
            $table->string('payment')->nullable()->change();
            $table->string('Livraison')->nullable()->change();
            $table->date('dateTot')->nullable()->change();
            $table->date('dateTard')->nullable()->change();
            $table->string('specificity')->nullable()->change();
            $table->string('id_prod')->nullable()->change();
            $table->string('image')->nullable()->change();
            $table->json('prodUsers')->nullable()->change();
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

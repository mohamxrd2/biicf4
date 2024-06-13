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
        Schema::create('comment', function (Blueprint $table) {
            $table->id();
            $table->integer('prixTrade')->nullable();
            $table->unsignedBigInteger('id_trader')->nullable();
            $table->string('code_unique')->nullable();
            $table->unsignedBigInteger('id_prod')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_trader')
                ->references('id')->on('users')
                ->onDelete('cascade');

            $table->foreign('id_prod')
                ->references('id')->on('produit_services')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comment');
    }
};

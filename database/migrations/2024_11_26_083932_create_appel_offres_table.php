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
        Schema::create('appel_offres', function (Blueprint $table) {
            $table->id();
            $table->string('product_name');
            $table->integer('quantity');
            $table->string('payment');
            $table->string('livraison');
            $table->date('date_tot');
            $table->date('date_tard');
            $table->time('time_start')->nullable();
            $table->time('time_end')->nullable();
            $table->string('day_period')->nullable();
            $table->text('specification')->nullable();
            $table->string('reference');
            $table->string('localite')->nullable();
            $table->json('prodUsers')->nullable();
            $table->string('code_unique')->nullable();
            $table->integer('lowestPricedProduct')->nullable();
            $table->binary('image')->nullable();
            $table->timestamps();

            $table->foreignId('id_sender')->constrained('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appel_offres');
    }
};

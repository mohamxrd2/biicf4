<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppeloffregrouperTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appeloffregrouper', function (Blueprint $table) {
            $table->id();
            $table->string('productName');
            $table->integer('quantity');
            $table->string('payment');
            $table->string('Livraison');
            $table->date('dateTot');
            $table->date('dateTard');
            $table->string('specificity')->nullable();
            $table->string('id_prod')->nullable(); // Assuming 'id_prod' is a string. Adjust type if necessary.
            $table->string('image')->nullable(); // Assuming 'image' stores the file path as a string. Adjust type if necessary.
            $table->json('prodUsers'); // Store prodUsers as JSON
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appeloffregrouper');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('promir', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Clé étrangère vers users
            $table->string('name');
            $table->string('last_stname');
            $table->string('user_name')->unique();
            $table->string('email')->unique();
            $table->string('phone_number')->unique();
            $table->unsignedBigInteger('system_client_id');
            $table->timestamps();
            $table->integer('mois_depuis_creation');

            // Définition de la clé étrangère
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('promir');
    }
};

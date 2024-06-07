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
        Schema::create('consommations', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->string('type', 255)->nullable();
            $table->string('conditionnement', 255);
            $table->string('format', 255)->nullable();
            $table->integer('qte');
            $table->integer('prix')->nullable();
            $table->string('frqce_cons', 255)->nullable();
            $table->string('jourAch_cons', 255)->nullable();
            $table->string('qualif_serv', 255)->nullable();;
            $table->string('specialité', 255)->nullable();
            $table->string('description', 255)->nullable();
            $table->string('zoneAct', 255);
            $table->string('villeCons', 255);
            $table->unsignedBigInteger('id_user')->nullable();


            $table->foreign('id_user')
                ->references('id')
                ->on('users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consommations');
    }
};

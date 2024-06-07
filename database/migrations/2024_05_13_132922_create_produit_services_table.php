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
        Schema::create('produit_services', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->nullable();
            $table->string('type', 255)->nullable();
            $table->string('condProd', 255)->nullable();
            $table->string('formatProd', 255)->nullable();
            $table->integer('qteProd_min')->nullable();
            $table->integer('qteProd_max')->nullable();
            $table->integer('prix')->nullable();
            $table->string('LivreCapProd', 255)->nullable();
            $table->string('photoProd1')->nullable();
            $table->string('photoProd2')->nullable();
            $table->string('photoProd3')->nullable();
            $table->string('photoProd4')->nullable();
            $table->string('videoProd')->nullable();
            $table->string('desrip', 255)->nullable();
            $table->string('qalifServ', 255)->nullable();
            $table->string('sepServ', 255)->nullable();
            $table->integer('qteServ')->nullable();
            $table->string('zonecoServ', 255)->nullable();
            $table->string('villeServ', 255)->nullable();
            $table->string('comnServ', 255)->nullable();
            $table->string('typeProdServ', 255)->nullable();
            $table->unsignedBigInteger('user_id')->nullable();

            $table->foreign('user_id')
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
        Schema::dropIfExists('produit_services');
    }
};

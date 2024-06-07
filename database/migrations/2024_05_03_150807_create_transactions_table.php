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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sender_user_id')->nullable();
            $table->unsignedBigInteger('sender_admin_id')->nullable();
            $table->unsignedBigInteger('receiver_user_id')->nullable();
            $table->unsignedBigInteger('receiver_admin_id')->nullable();
            $table->enum('type', ['Envoie', 'Reception', 'Depot', 'Retrait', 'Commmission']);
            $table->decimal('amount', 10, 2);
            $table->foreign('sender_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('sender_admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->foreign('receiver_user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('receiver_admin_id')->references('id')->on('admins')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

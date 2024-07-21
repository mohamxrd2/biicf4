<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('groupagefact', function (Blueprint $table) {
            $table->id();
            $table->json('usersenders');
            $table->string('code_unique');
            $table->timestamp('start_time')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->boolean('notified')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('groupagefact', function (Blueprint $table) {
            //
        });
    }
};

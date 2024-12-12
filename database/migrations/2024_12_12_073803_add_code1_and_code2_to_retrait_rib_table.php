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
        Schema::table('retrait_rib', function (Blueprint $table) {
            //
            $table->integer('code1')->nullable(); // code1 de type int
            $table->integer('code2')->nullable(); // code2 de type int
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            //
            $table->dropColumn('code1');
            $table->dropColumn('code2');
        });
    }
};

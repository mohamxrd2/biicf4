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
        Schema::table('produit_services', function (Blueprint $table) {
            $table->string('continent')->nullable()->after('qteServ');
            $table->string('Sous-Region')->nullable()->after('continent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produit_services', function (Blueprint $table) {
            //
        });
    }
};

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
            $table->string('reference')->nullable()->after('name');
            $table->text('origine')->nullable()->after('qteProd_max');
            $table->text('Particularite')->nullable()->after('desrip');

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

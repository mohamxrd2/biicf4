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
        Schema::table('consommations', function (Blueprint $table) {
            $table->string('reference')->nullable()->after('name');
            $table->text('origine')->nullable()->after('qte');  // First instance of 'origine'
            $table->text('Particularite')->nullable()->after('format');
            // Remove the duplicate line
            // $table->text('origine')->nullable()->after('Particularite');  // This line should be removed

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consommations', function (Blueprint $table) {
            //
        });
    }
};

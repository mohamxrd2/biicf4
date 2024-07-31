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
        Schema::table('offre_groupe', function (Blueprint $table) {
            // Drop the 'differance' column
            $table->dropColumn('differance');

            // Add the 'notified' column as boolean
            $table->boolean('notified')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offre_groupe');
    }
};

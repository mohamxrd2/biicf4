<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('achat_direct', function (Blueprint $table) {
            $table->json('details_paiement')->nullable()->after('type_achat'); // Ajout après type_achat
        });
    }

    public function down()
    {
        Schema::table('achat_direct', function (Blueprint $table) {
            $table->dropColumn('details_paiement');
        });
    }
};

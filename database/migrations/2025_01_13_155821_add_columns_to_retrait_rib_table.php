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
            $table->string('cle_iban')->nullable(); // Clé IBAN
            $table->string('code_bic')->nullable(); // Code BIC
            $table->string('code_bank')->nullable(); // Code de la banque
            $table->string('code_guiche')->nullable(); // Code guichet
            $table->string('numero_compte')->nullable(); // Numéro de compte
            $table->string('iban')->nullable(); // IBAN complet
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('retrait_rib', function (Blueprint $table) {
            $table->dropColumn(['cle_iban', 'code_bic', 'code_bank', 'code_guiche', 'numero_compte', 'iban']);
        });
    }
};

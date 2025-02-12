<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->date('next_payment_date')->nullable()->after('date_fin');
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('next_payment_date');
        });
    }

    public function down()
    {
        Schema::table('tontines', function (Blueprint $table) {
            $table->dropColumn('next_payment_date');
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
};

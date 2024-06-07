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
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // Clé primaire 'id'
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('prenom_user')->nullable();
            $table->string('username')->unique();
            $table->string('actor_type');
            $table->string('gender')->nullable();
            $table->string('age')->nullable();
            $table->string('social_status')->nullable();
            $table->string('company_size')->nullable();
            $table->string('service_type')->nullable();
            $table->string('organization_type')->nullable();
            $table->string('second_organization_type')->nullable();
            $table->string('communication_type')->nullable();
            $table->string('mena_type')->nullable();
            $table->string('mena_status')->nullable();
            $table->string('sector')->nullable();
            $table->string('industry')->nullable();
            $table->string('construction')->nullable();
            $table->string('commerce')->nullable();
            $table->string('services')->nullable();
            $table->string('country');
            $table->string('phone');
            $table->string('local_area');
            $table->string('address');
            $table->string('active_zone')->nullable();
            $table->string('photo')->default('https://img.myloview.com/stickers/default-avatar-profile-icon-vector-social-media-user-photo-700-205577532.jpg');
            $table->timestamp('last_seen')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
    }
};


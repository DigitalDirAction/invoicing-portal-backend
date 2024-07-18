<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name', 25)->nullable();
            $table->string('last_name', 25)->nullable();
            $table->string('name', 50);
            $table->string('email', 75)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('company', 50);
            $table->string('industry', 50);
            $table->string('country', 50);
            $table->string('language', 50)->nullable();
            $table->string('gender', 10)->nullable();
            $table->string('phone_number', 15);
            $table->string('two_factor_code')->nullable();
            $table->dateTime('two_factor_expires_at')->nullable();
            $table->string('profile_image')->nullable();
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};

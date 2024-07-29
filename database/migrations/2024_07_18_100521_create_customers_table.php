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
        Schema::create('customers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('customer_type', 12);
            $table->string('first_name', 20);
            $table->string('last_name', 20);
            $table->string('company_name', 200);
            $table->string('currency', 50);
            $table->string('email', 50);
            $table->string('phone_number', 15)->nullable();
            $table->string('mobile_number', 15)->nullable();
            $table->text('address');
            $table->string('logo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};

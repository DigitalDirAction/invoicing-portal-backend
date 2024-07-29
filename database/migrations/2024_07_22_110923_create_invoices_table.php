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
        Schema::create('invoices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('bank_id');
            $table->foreign('bank_id')->references('id')->on('banking_details');
            $table->string('invoice_number', 15);
            $table->string('currency', 10);
            $table->date('invoice_date');
            $table->date('due_date');
            $table->string('quantity_text');
            $table->string('rate_text');
            $table->string('tax_text');
            $table->string('amount_text');
            $table->string('sub_total');
            $table->string('total_amount');
            $table->text('customer_note');
            $table->string('status', 20);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

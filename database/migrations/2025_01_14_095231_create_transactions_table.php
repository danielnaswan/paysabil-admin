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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id('id')->primary();
            $table->timestamp('transaction_date');
            $table->enum('status', ['PENDING', 'COMPLETED', 'FAILED', 'CANCELLED']);
            $table->decimal('amount', 10, 2);
            $table->text('meal_details');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignId('qr_code_id')->constrained('qr_codes')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};

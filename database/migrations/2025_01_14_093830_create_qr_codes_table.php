<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('code')->unique();  // Added this line for unique QR code identifier
            $table->json('service_details');
            $table->datetime('generated_date');
            $table->datetime('expiry_date');
            $table->enum('status', ['ACTIVE', 'EXPIRED', 'USED', 'INVALID'])->default('ACTIVE');
            $table->foreignUuid('vendor_id')->constrained('vendors')->onDelete('cascade');
            $table->foreignUuid('service_id')->constrained('services')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_codes');
    }
};
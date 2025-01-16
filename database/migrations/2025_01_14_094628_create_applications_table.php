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
        Schema::create('applications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');                                                                            // Application title/purpose
            $table->text('description')->nullable();                                                            // Detailed description
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->datetime('submission_date');
            $table->string('document_url', 255);                                                        // URL to stored PDF
            $table->string('document_name', 255);                                                       // Original filename
            $table->integer('document_size');                                                                   // File size in bytes
            $table->text('admin_remarks')->nullable();                                                          // Admin feedback/comments
            $table->foreignUuid('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');                          // Admin who reviewed
            $table->datetime('reviewed_at')->nullable();                                                        // When was it reviewed
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applications');
    }
};

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
            $table->id('id')->primary();
            $table->string('title');                                                                            
            $table->text('description')->nullable();                                                            
            $table->enum('status', ['PENDING', 'APPROVED', 'REJECTED'])->default('PENDING');
            $table->datetime('submission_date');
            $table->string('document_url', 255);                                                        
            $table->string('document_name', 255);                                                       
            $table->integer('document_size');                                                                   
            $table->text('admin_remarks')->nullable();                                                          
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('reviewed_by')->nullable()->constrained('users');                          
            $table->datetime('reviewed_at')->nullable();                                                        
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

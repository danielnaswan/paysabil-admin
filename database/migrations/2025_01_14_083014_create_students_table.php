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
        Schema::create('students', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('full_name', 100);
            $table->string('matrix_no', 20)->unique();
            $table->string('profile_picture_url', 255)->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Changed to foreignId() to match template's user id type
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};

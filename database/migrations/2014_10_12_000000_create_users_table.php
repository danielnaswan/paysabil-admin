<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\UserRole;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            // $table->bigInteger('phone')->nullable();
            $table->string('phone_number', 15)->nullable();
            // $table->enum('status', UserRole::values())->default(UserRole::ADMIN->value);
            $table->enum('role', ['ADMIN', 'VENDOR', 'STUDENT']);
            $table->string('location')->nullable();
            $table->string('about_me')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('users');
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number', 'role');
        });
    }
}

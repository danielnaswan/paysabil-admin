<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        DB::table('admins')->truncate();
        DB::table('users')->truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = now();
        
        $userId = DB::table('users')->insertGetId([
            'name' => 'admin',
            'email' => env('MAIL_USERNAME', 'admin@example.com'),
            'phone_number' => '0193983685',
            'role' => UserRole::ADMIN,
            'password' => Hash::make('secret'),
            'created_at' => $now,
            'updated_at' => $now
        ]);

        DB::table('admins')->insert([
            'user_id' => $userId,
            'full_name' => 'admin',
            'department' => 'admin',
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\UserRole;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            // 'id' => 1,
            'name'              => 'admin',
            'email'             => env('MAIL_USERNAME'),
            'phone_number'      => '0193983685',
            'role'              => UserRole::ADMIN,
            'password'          => Hash::make('secret'),
            'created_at'        => now(),
            'updated_at'        => now()
        ]);
    }
}

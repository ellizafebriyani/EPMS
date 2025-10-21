<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->where('email','admin@example.com')->delete();

        DB::table('users')->insert([
            'name' => 'Admin EPMS',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password2025'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

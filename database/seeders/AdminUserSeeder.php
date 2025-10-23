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
        // Hapus user lama jika ada
        DB::table('users')->whereIn('email', [
            'admin@example.com',
            'user@example.com'
        ])->delete();

        // Admin user
        DB::table('users')->insert([
            'name' => 'Admin EPMS',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password2025'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin EPMS',
            'email' => 'ellizafebriyani@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('12345'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('users')->insert([
            'name' => 'Admin EPMS',
            'email' => 'hikayatioktaviyani@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('678910'),
            'remember_token' => Str::random(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}

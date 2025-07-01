<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin Kabupaten Badung',
            'email' => 'admin@badung.go.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin'
        ]);
        
        // Create sample admin
        User::create([
            'name' => 'Admin Test',
            'email' => 'test@badung.go.id',
            'password' => Hash::make('password'),
            'role' => 'admin'
        ]);
    }
}

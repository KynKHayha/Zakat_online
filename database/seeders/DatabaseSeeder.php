<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat akun Admin (Amil)
        User::firstOrCreate(
            ['email' => 'admin@zakatzakat.com'],
            [
                'name'     => 'Admin Zakat',
                'password' => Hash::make('admin123456'),
                'role'     => 'admin',
                'phone'    => '08123456789',
                'address'  => 'Kantor Zakat Online, Jakarta',
            ]
        );

        // Buat akun Muzakki contoh
        User::firstOrCreate(
            ['email' => 'muzakki@example.com'],
            [
                'name'     => 'Ahmad Muzakki',
                'password' => Hash::make('password123'),
                'role'     => 'muzakki',
                'phone'    => '08987654321',
                'address'  => 'Jl. Mawar No. 10, Bandung',
            ]
        );

        $this->command->info('✅ Seeder berhasil! Akun tersedia:');
        $this->command->info('   Admin  → admin@zakatzakat.com / admin123456');
        $this->command->info('   Muzakki→ muzakki@example.com / password123');
    }
}

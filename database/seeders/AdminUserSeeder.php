<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin Utama',
            'email' => 'admin.utama@tropistee.com',
            'password' => Hash::make('password'),
            'role' => 'utama',
        ]);

        User::create([
            'name' => 'Admin Pesanan',
            'email' => 'admin.pesanan@tropistee.com',
            'password' => Hash::make('password'),
            'role' => 'pesanan',
        ]);

        User::create([
            'name' => 'Admin Produksi',
            'email' => 'admin.produksi@tropistee.com',
            'password' => Hash::make('password'),
            'role' => 'produksi',
        ]);

        User::create([
            'name' => 'Admin Distribusi',
            'email' => 'admin.distribusi@tropistee.com',
            'password' => Hash::make('password'),
            'role' => 'distribusi',
        ]);

        User::create([
            'name' => 'Admin Finansial',
            'email' => 'admin.finansial@tropistee.com',
            'password' => Hash::make('password'),
            'role' => 'finansial',
        ]);
    }
}

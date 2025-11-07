<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Supervisor PGD',
                'email' => 'supervisor@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'supervisi',
                'bagian' => null,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'bagian' => 'PGB',
            ],
            [
                'name' => 'Ani Wijaya',
                'email' => 'ani@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'bagian' => 'PGB',
            ],
            [
                'name' => 'Candra Pratama',
                'email' => 'candra@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'perizinan',
                'bagian' => 'PKJ',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'perizinan',
                'bagian' => 'PKJ',
            ],
        ];

        foreach ($users as $userData) {
            // Cek apakah email sudah ada, kalau ada update, kalau belum create
            User::updateOrCreate(
                ['email' => $userData['email']], // Cari berdasarkan email
                $userData                         // Data yang mau di-insert/update
            );
        }
    }
}
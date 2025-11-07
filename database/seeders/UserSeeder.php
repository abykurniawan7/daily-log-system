<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Updated structure (10 users):
     * - 1 Admin (user management)
     * - 1 Super Admin (supervisi - full access)
     * - 1 Kabag PGB (leader PGB)
     * - 4 PGB Staff
     * - 2 PKJ Staff
     * - 1 Guest (testing/demo)
     */
    public function run(): void
    {
        $users = [
            // ==========================================
            // 1. ADMIN (User Management)
            // ==========================================
            [
                'name' => 'Admin Worklog',
                'email' => 'admin@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'bagian' => null,
            ],
            
            // ==========================================
            // 2. SUPER ADMIN (Supervisi - Full Access)
            // ==========================================
            [
                'name' => 'Super Admin PGD',
                'email' => 'superadmin@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'supervisi',
                'bagian' => null,
            ],
            
            // ==========================================
            // 3. KABAG PGB (Leader PGB)
            // ==========================================
            [
                'name' => 'Kabag PGB',
                'email' => 'kabag.pgb@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'kabag_pgb',
                'bagian' => 'PGB',
            ],
            
            // ==========================================
            // 4. PGB STAFF (4 orang)
            // ==========================================
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
                'name' => 'Eko Prasetyo',
                'email' => 'eko@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'bagian' => 'PGB',
            ],
            [
                'name' => 'Fitri Handayani',
                'email' => 'fitri@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'karyawan',
                'bagian' => 'PGB',
            ],
            
            // ==========================================
            // 5. PKJ STAFF (2 orang)
            // ==========================================
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
            
            // ==========================================
            // 6. GUEST (Testing/Demo)
            // ==========================================
            [
                'name' => 'Guest User',
                'email' => 'guest@bpdbali.co.id',
                'password' => Hash::make('password'),
                'role' => 'guest',
                'bagian' => null,
            ],
        ];

        // Insert or update users
        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']], // Check by email
                $userData // Update/Insert with this data
            );
        }
        
        // Log hasil
        $this->command->info('✅ UserSeeder completed!');
        $this->command->info('   - Total users: ' . User::count());
        $this->command->info('   - Admin: ' . User::where('role', 'admin')->count());
        $this->command->info('   - Supervisi: ' . User::where('role', 'supervisi')->count());
        $this->command->info('   - Kabag PGB: ' . User::where('role', 'kabag_pgb')->count());
        $this->command->info('   - PGB Staff: ' . User::where('bagian', 'PGB')->where('role', 'karyawan')->count());
        $this->command->info('   - PKJ Staff: ' . User::where('bagian', 'PKJ')->count());
        $this->command->info('   - Guest: ' . User::where('role', 'guest')->count());
    }
}
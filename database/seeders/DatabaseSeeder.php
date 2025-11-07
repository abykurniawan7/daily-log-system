<?php

namespace Database\Seeders;

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
        // Urutan PENTING! Parent dulu, baru child
        $this->call([
            DivisionSeeder::class,   // 1. Division dulu (parent)
            TestingDataSeeder::class,   // Then seed testing data
            UserSeeder::class,       // 2. User kedua (parent)
            ProjectSeeder::class,    // 3. Project ketiga (butuh Division & User)
            ActivitySeeder::class,   // 4. Activity terakhir (butuh Project & User)
        ]);
    }
}
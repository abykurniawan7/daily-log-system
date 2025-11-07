<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Division;
use App\Models\Project;
use App\Models\Activity;
use App\Models\RoleRequest;
use Illuminate\Support\Facades\Hash;

class TestingDataSeeder extends Seeder
{
    public function run(): void
    {
        // ================================
        // 1. CREATE USERS
        // ================================
        
        // Admin
        $admin = User::create([
            'name' => 'Admin Worklog',
            'email' => 'admin@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'bagian' => null,
        ]);

        // Supervisi
        $supervisi = User::create([
            'name' => 'Supervisi Team',
            'email' => 'supervisi@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'supervisi',
            'bagian' => null,
        ]);

        // Karyawan PGB
        $karyawan1 = User::create([
            'name' => 'Anggrek Pontianak',
            'email' => 'anggrek@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'bagian' => 'PGB',
        ]);

        $karyawan2 = User::create([
            'name' => 'Budi Santoso',
            'email' => 'budi@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'karyawan',
            'bagian' => 'PGB',
        ]);

        // Perizinan PKJ
        $perizinan1 = User::create([
            'name' => 'Panjat Pinang',
            'email' => 'panjat@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'perizinan',
            'bagian' => 'PKJ',
        ]);

        $perizinan2 = User::create([
            'name' => 'Dewi Lestari',
            'email' => 'dewi@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'perizinan',
            'bagian' => 'PKJ',
        ]);

        // Guest User
        $guest = User::create([
            'name' => 'Guest User',
            'email' => 'guest@bpdbali.co.id',
            'password' => Hash::make('password'),
            'role' => 'guest',
            'bagian' => null,
        ]);

        echo "✅ Users created (7 users)\n";

        // ================================
        // 2. GET DIVISIONS (✅ IMPROVED!)
        // ================================
        
        $divisions = Division::all();
        echo "✅ Found {$divisions->count()} divisions\n";

        // ✅ FIX: Ambil division pertama sebagai fallback
        $divisionPGB = $divisions->firstWhere('nama_divisi', 'Pembangunan') 
                    ?? $divisions->firstWhere('nama_divisi', 'PGB')
                    ?? $divisions->first();
                    
        $divisionPKJ = $divisions->firstWhere('nama_divisi', 'Perizinan') 
                    ?? $divisions->firstWhere('nama_divisi', 'PKJ')
                    ?? $divisions->skip(1)->first()
                    ?? $divisions->first();

        // Debug: Tampilkan division yang dipakai
        echo "📌 Using Division PGB: {$divisionPGB->nama_divisi}\n";
        echo "📌 Using Division PKJ: {$divisionPKJ->nama_divisi}\n";

        // ================================
        // 3. CREATE PROJECTS (✅ FINAL - SEMUA ENUM BENAR!)
        // ================================

        $projects = [
            [
                'nama_project' => 'Pembangunan Kantor Cabang Denpasar',
                'pemilik_project_id' => $divisionPGB->id,
                'pic_proyek_id' => $karyawan1->id,
                'user_id' => $karyawan1->id,
                'tanggal_inisiasi' => now()->subDays(30),
                'target_implementasi' => now()->addMonths(6),
                'urgensi' => 'Very High', // ✅ Kapitalisasi + spasi
                'sifat_project' => 'Strategis',
                'deskripsi' => 'Proyek pembangunan gedung kantor cabang baru di Denpasar, Bali. Target selesai 6 bulan dengan budget besar.',
                'status' => 'Progress', // ✅ Kapitalisasi
            ],
            [
                'nama_project' => 'Renovasi Kantor Pusat',
                'pemilik_project_id' => $divisionPGB->id,
                'pic_proyek_id' => $karyawan2->id,
                'user_id' => $karyawan2->id,
                'tanggal_inisiasi' => now()->subDays(15),
                'target_implementasi' => now()->addMonths(3),
                'urgensi' => 'Medium',
                'sifat_project' => 'Rutin',
                'deskripsi' => 'Renovasi total kantor pusat termasuk upgrade sistem listrik dan AC. Lokasi: Denpasar, Bali.',
                'status' => 'Progress',
            ],
            [
                'nama_project' => 'Pengurusan IMB Gedung Baru',
                'pemilik_project_id' => $divisionPKJ->id,
                'pic_proyek_id' => $perizinan1->id,
                'user_id' => $perizinan1->id,
                'tanggal_inisiasi' => now()->subDays(20),
                'target_implementasi' => now()->addMonths(2),
                'urgensi' => 'High',
                'sifat_project' => 'Strategis',
                'deskripsi' => 'Proses perizinan IMB untuk gedung kantor baru di Badung, Bali. Termasuk konsultasi dengan Dinas Penanaman Modal.',
                'status' => 'Progress',
            ],
            [
                'nama_project' => 'Perpanjangan SITU',
                'pemilik_project_id' => $divisionPKJ->id,
                'pic_proyek_id' => $perizinan2->id,
                'user_id' => $perizinan2->id,
                'tanggal_inisiasi' => now()->subDays(10),
                'target_implementasi' => now()->addMonth(),
                'urgensi' => 'Medium',
                'sifat_project' => 'Rutin',
                'deskripsi' => 'Perpanjangan Surat Izin Tempat Usaha untuk kantor cabang Denpasar. Masa berlaku akan habis bulan depan.',
                'status' => 'Progress',
            ],
            [
                'nama_project' => 'Audit Perizinan Tahunan',
                'pemilik_project_id' => $divisionPKJ->id,
                'pic_proyek_id' => $perizinan1->id,
                'user_id' => $perizinan1->id,
                'tanggal_inisiasi' => now()->subDays(5),
                'target_implementasi' => now()->addMonths(4),
                'urgensi' => 'Low',
                'sifat_project' => 'Rutin',
                'deskripsi' => 'Audit kelengkapan dokumen perizinan tahunan untuk semua cabang di Bali. Termasuk pengecekan compliance.',
                'status' => 'Pending', // ✅ Variasi status
            ],
        ];

        foreach ($projects as $projectData) {
            Project::create($projectData);
        }

        echo "✅ Projects created (5 projects)\n";
        
        // ================================
        // 4. CREATE ACTIVITIES
        // ================================
        
        $allProjects = Project::all();
        $activityCount = 0;

        foreach ($allProjects as $project) {
            $numActivities = rand(3, 5);
            
            for ($i = 0; $i < $numActivities; $i++) {
                Activity::create([
                    'project_id' => $project->id,
                    'user_id' => $project->pic_proyek_id,
                    'nama_aktivitas' => 'Aktivitas ' . ($i + 1) . ' - ' . $project->nama_project,
                    'jenis_kegiatan' => ['rapat', 'survey', 'dokumentasi', 'koordinasi'][array_rand(['rapat', 'survey', 'dokumentasi', 'koordinasi'])],
                    'tanggal_mulai' => now()->subDays(rand(1, 30)),
                    'tanggal_selesai' => rand(0, 1) ? now()->subDays(rand(0, 15)) : null,
                    'status' => ['progress', 'pending', 'done'][array_rand(['progress', 'pending', 'done'])],
                    'deskripsi' => 'Deskripsi aktivitas untuk ' . $project->nama_project,
                    'lampiran' => null,
                ]);
                $activityCount++;
            }
        }

        echo "✅ Activities created ({$activityCount} activities)\n";

        // ================================
        // 5. CREATE ROLE REQUESTS
        // ================================
        
        RoleRequest::create([
            'user_id' => $guest->id,
            'requested_role' => 'perizinan',
            'requested_bagian' => 'PKJ',
            'deskripsi' => 'Saya adalah karyawan baru di bagian Perizinan (PKJ) dan membutuhkan akses untuk mengelola dokumen perizinan dan aktivitas terkait. Saya sudah menyelesaikan training onboarding dan siap untuk mulai bekerja dengan sistem WorkLog.',
            'status' => 'pending',
            'admin_notes' => null,
        ]);

        echo "✅ Role requests created (1 pending request)\n";

        echo "\n=================================\n";
        echo "✅ SEEDING COMPLETED SUCCESSFULLY!\n";
        echo "=================================\n";
        echo "📧 Login Credentials:\n";
        echo "Admin: admin@bpdbali.co.id / password\n";
        echo "Supervisi: supervisi@bpdbali.co.id / password\n";
        echo "Karyawan PGB: anggrek@bpdbali.co.id / password\n";
        echo "Perizinan PKJ: panjat@bpdbali.co.id / password\n";
        echo "Guest: guest@bpdbali.co.id / password\n";
        echo "=================================\n\n";
    }
}
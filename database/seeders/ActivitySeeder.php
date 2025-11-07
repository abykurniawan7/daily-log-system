<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Activity;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $activities = [
            // Activities untuk Project 1 (Upgrade Sistem ATM)
            [
                'project_id' => 1,
                'user_id' => 2, // Budi
                'tanggal_mulai' => Carbon::now()->subDays(25),
                'tanggal_selesai' => Carbon::now()->subDays(23),
                'nama_aktivitas' => 'Kick-off Meeting dengan Operasional',
                'jenis_kegiatan' => 'Meeting',
                'status' => 'Done',
                'deskripsi' => 'Meeting awal untuk membahas scope project',
                'lampiran' => null,
            ],
            [
                'project_id' => 1,
                'user_id' => 2,
                'tanggal_mulai' => Carbon::now()->subDays(22),
                'tanggal_selesai' => Carbon::now()->subDays(18),
                'nama_aktivitas' => 'Analisis Kebutuhan Sistem',
                'jenis_kegiatan' => 'Analisis',
                'status' => 'Done',
                'deskripsi' => 'Menganalisis requirement dari divisi operasional',
                'lampiran' => null,
            ],
            [
                'project_id' => 1,
                'user_id' => 2,
                'tanggal_mulai' => Carbon::now()->subDays(15),
                'tanggal_selesai' => null,
                'nama_aktivitas' => 'Pembuatan Dokumen BRD',
                'jenis_kegiatan' => 'Dokumentasi',
                'status' => 'Progress',
                'deskripsi' => 'Menyusun Business Requirement Document',
                'lampiran' => null,
            ],
            
            // Activities untuk Project 2 (Sistem Absensi)
            [
                'project_id' => 2,
                'user_id' => 3, // Ani
                'tanggal_mulai' => Carbon::now()->subDays(18),
                'tanggal_selesai' => Carbon::now()->subDays(17),
                'nama_aktivitas' => 'Koordinasi dengan SDM',
                'jenis_kegiatan' => 'Meeting',
                'status' => 'Done',
                'deskripsi' => 'Diskusi requirement sistem absensi',
                'lampiran' => null,
            ],
            [
                'project_id' => 2,
                'user_id' => 3,
                'tanggal_mulai' => Carbon::now()->subDays(14),
                'tanggal_selesai' => null,
                'nama_aktivitas' => 'Riset Aplikasi Absensi',
                'jenis_kegiatan' => 'Riset',
                'status' => 'Progress',
                'deskripsi' => 'Survey dan benchmark aplikasi absensi existing',
                'lampiran' => null,
            ],
            
            // Activities untuk Project 3 (Perizinan - PKJ)
            [
                'project_id' => 3,
                'user_id' => 4, // Candra (PKJ)
                'tanggal_mulai' => Carbon::now()->subDays(12),
                'tanggal_selesai' => Carbon::now()->subDays(10),
                'nama_aktivitas' => 'Review Regulasi OJK',
                'jenis_kegiatan' => 'Riset',
                'status' => 'Done',
                'deskripsi' => 'Mempelajari regulasi terkait pembukaan rekening online',
                'lampiran' => null,
            ],
            [
                'project_id' => 3,
                'user_id' => 4,
                'tanggal_mulai' => Carbon::now()->subDays(8),
                'tanggal_selesai' => null,
                'nama_aktivitas' => 'Penyusunan Dokumen Perizinan',
                'jenis_kegiatan' => 'Dokumentasi',
                'status' => 'Progress',
                'deskripsi' => 'Membuat dokumen pengajuan ke OJK',
                'lampiran' => null,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
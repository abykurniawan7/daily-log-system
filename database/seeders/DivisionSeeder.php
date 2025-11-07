<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Division;
use Illuminate\Support\Facades\DB;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * 14 Divisi Resmi BPD Bali
     * Display Format: "Nama Divisi (KODE)"
     * Example: "Sumber Daya Manusia (SDM)"
     */
    public function run(): void
    {
        // ✅ HAPUS semua divisi lama dulu (Production: comment line ini!)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Division::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
        // ✅ 14 DIVISI RESMI BPD BALI
        $divisions = [
            [
                'kode_divisi' => 'SDM',
                'nama_divisi' => 'Sumber Daya Manusia',
                'deskripsi' => 'Mengelola rekrutmen, pelatihan, pengembangan karier, dan kesejahteraan pegawai.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'MRO',
                'nama_divisi' => 'Manajemen Risiko',
                'deskripsi' => 'Mengidentifikasi, menilai, memantau, dan mengendalikan risiko operasional, kredit, pasar, dan lainnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'RENSTRA',
                'nama_divisi' => 'Perencanaan Strategis',
                'deskripsi' => 'Menyusun strategi bisnis, rencana jangka menengah, dan evaluasi kinerja strategis bank.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'KRK',
                'nama_divisi' => 'Kredit Ritel & Konsumer',
                'deskripsi' => 'Mengelola produk dan layanan kredit untuk nasabah ritel dan individu.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'PGD',
                'nama_divisi' => 'Pengembangan Digital',
                'deskripsi' => 'Mengembangkan inovasi digital, aplikasi perbankan, dan transformasi layanan berbasis teknologi.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'UMA',
                'nama_divisi' => 'Umum & Aset',
                'deskripsi' => 'Mengelola fasilitas, aset tetap, logistik, dan urusan umum bank.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'TRS',
                'nama_divisi' => 'Treasury',
                'deskripsi' => 'Mengelola likuiditas, investasi, dan kegiatan pasar uang serta valuta asing bank.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'DJA',
                'nama_divisi' => 'Dana & Jasa',
                'deskripsi' => 'Mengelola produk dana (tabungan, giro, deposito) dan layanan jasa perbankan lainnya.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'OKA',
                'nama_divisi' => 'Operasional, Keuangan & Akuntansi',
                'deskripsi' => 'Menangani proses operasional, pembukuan, dan pelaporan keuangan bank.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'SEKPER',
                'nama_divisi' => 'Sekretaris Perusahaan',
                'deskripsi' => 'Mengelola komunikasi korporat, administrasi direksi, dan hubungan dengan stakeholder.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'KPN',
                'nama_divisi' => 'Kepatuhan',
                'deskripsi' => 'Memastikan seluruh aktivitas bank sesuai peraturan OJK, BI, dan hukum yang berlaku.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'KPI',
                'nama_divisi' => 'Kredit Korporasi',
                'deskripsi' => 'Mengelola pembiayaan dan hubungan dengan nasabah korporasi (perusahaan).',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'TIF',
                'nama_divisi' => 'Teknologi Informasi',
                'deskripsi' => 'Menangani infrastruktur TI, keamanan data, dan sistem aplikasi perbankan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'kode_divisi' => 'SKAI',
                'nama_divisi' => 'Satuan Kerja Audit Intern & Anti Fraud',
                'deskripsi' => 'Melakukan audit internal serta pencegahan dan investigasi kecurangan.',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        Division::insert($divisions);
        
        $this->command->info('');
        $this->command->info('✅ 14 Divisi BPD Bali berhasil ditambahkan!');
        $this->command->info('📋 Display Format: "Nama Divisi (KODE)"');
        $this->command->info('');
        $this->command->table(
            ['Kode', 'Display Format', 'Tugas Utama'],
            collect($divisions)->map(function($div) {
                return [
                    $div['kode_divisi'],
                    $div['nama_divisi'] . ' (' . $div['kode_divisi'] . ')',
                    substr($div['deskripsi'], 0, 40) . '...'
                ];
            })->toArray()
        );
    }
}
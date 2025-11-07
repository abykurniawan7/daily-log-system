<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use App\Models\Division;
use Carbon\Carbon;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Get users untuk PIC (fallback ke ID jika belum ada user)
        $adminSupervisi = User::where('role', 'admin')->orWhere('role', 'supervisi')->first();
        $pkjUser = User::where('bagian', 'PKJ')->first();
        $pgbUser = User::where('bagian', 'PGB')->first();
        
        $pic1 = $adminSupervisi ? $adminSupervisi->id : 1;
        $pic2 = $pkjUser ? $pkjUser->id : 2;
        $pic3 = $pgbUser ? $pgbUser->id : 3;
        
        // Get division IDs by kode
        $divSDM = Division::where('kode_divisi', 'SDM')->first()?->id ?? 1;
        $divMRO = Division::where('kode_divisi', 'MRO')->first()?->id ?? 2;
        $divRENSTRA = Division::where('kode_divisi', 'RENSTRA')->first()?->id ?? 3;
        $divKRK = Division::where('kode_divisi', 'KRK')->first()?->id ?? 4;
        $divPGD = Division::where('kode_divisi', 'PGD')->first()?->id ?? 5;
        $divUMA = Division::where('kode_divisi', 'UMA')->first()?->id ?? 6;
        $divTRS = Division::where('kode_divisi', 'TRS')->first()?->id ?? 7;
        $divDJA = Division::where('kode_divisi', 'DJA')->first()?->id ?? 8;
        $divOKA = Division::where('kode_divisi', 'OKA')->first()?->id ?? 9;
        $divSEKPER = Division::where('kode_divisi', 'SEKPER')->first()?->id ?? 10;
        $divKPN = Division::where('kode_divisi', 'KPN')->first()?->id ?? 11;
        $divKPI = Division::where('kode_divisi', 'KPI')->first()?->id ?? 12;
        $divTIF = Division::where('kode_divisi', 'TIF')->first()?->id ?? 13;
        $divSKAI = Division::where('kode_divisi', 'SKAI')->first()?->id ?? 14;
        
        $projects = [
            // 1. DIVISI TIF - Teknologi Informasi
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(30),
                'target_implementasi' => 'Q4 2025',
                'nama_project' => 'Upgrade Sistem Core Banking',
                'urgensi' => 'Very High',
                'sifat_project' => 'RBB',
                'deskripsi' => 'Upgrade sistem core banking untuk meningkatkan performa dan keamanan transaksi nasabah',
                'pemilik_project_id' => $divTIF,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic1,
                'status' => 'Progress',
            ],
            
            // 2. DIVISI SDM - Sumber Daya Manusia
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(20),
                'target_implementasi' => 'Q1 2026',
                'nama_project' => 'Sistem Absensi & Payroll Digital',
                'urgensi' => 'High',
                'sifat_project' => 'Non RBB',
                'deskripsi' => 'Implementasi sistem absensi digital terintegrasi dengan payroll untuk seluruh karyawan BPD Bali',
                'pemilik_project_id' => $divSDM,
                'pic_proyek_id' => $pic3,
                'user_id' => $pic1,
                'status' => 'Progress',
            ],
            
            // 3. DIVISI PGD - Pengembangan Digital
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(15),
                'target_implementasi' => 'Q4 2025',
                'nama_project' => 'Mobile Banking Super App',
                'urgensi' => 'Very High',
                'sifat_project' => 'RBB',
                'deskripsi' => 'Pengembangan mobile banking dengan fitur lengkap (transfer, QRIS, investasi, marketplace)',
                'pemilik_project_id' => $divPGD,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic2,
                'status' => 'Progress',
            ],
            
            // 4. DIVISI SKAI - Audit Internal
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(45),
                'target_implementasi' => 'Q3 2025',
                'nama_project' => 'Audit Internal Tahunan 2025',
                'urgensi' => 'High',
                'sifat_project' => 'TL Audit',
                'deskripsi' => 'Pelaksanaan audit internal menyeluruh terhadap seluruh divisi dan cabang BPD Bali',
                'pemilik_project_id' => $divSKAI,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic1,
                'status' => 'Done',
            ],
            
            // 5. DIVISI DJA - Dana & Jasa
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(10),
                'target_implementasi' => 'Q1 2026',
                'nama_project' => 'Campaign Tabungan Bali Pusaka',
                'urgensi' => 'Medium',
                'sifat_project' => 'Non RBB',
                'deskripsi' => 'Kampanye promosi produk tabungan berjangka dengan bunga kompetitif',
                'pemilik_project_id' => $divDJA,
                'pic_proyek_id' => $pic3,
                'user_id' => $pic1,
                'status' => 'Pending',
            ],
            
            // 6. DIVISI RENSTRA - Perencanaan Strategis
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(60),
                'target_implementasi' => 'Q4 2025',
                'nama_project' => 'Rencana Strategis 2026-2030',
                'urgensi' => 'Very High',
                'sifat_project' => 'RBB',
                'deskripsi' => 'Penyusunan rencana strategis jangka menengah BPD Bali periode 2026-2030',
                'pemilik_project_id' => $divRENSTRA,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic1,
                'status' => 'Progress',
            ],
            
            // 7. DIVISI KPN - Kepatuhan
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(25),
                'target_implementasi' => 'Q1 2026',
                'nama_project' => 'Implementasi Peraturan OJK Terbaru',
                'urgensi' => 'High',
                'sifat_project' => 'Regulator',
                'deskripsi' => 'Penyesuaian SOP dan sistem untuk comply dengan regulasi OJK terbaru',
                'pemilik_project_id' => $divKPN,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic2,
                'status' => 'Progress',
            ],
            
            // 8. DIVISI UMA - Umum & Aset
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(35),
                'target_implementasi' => 'Q1 2026',
                'nama_project' => 'Renovasi Kantor Cabang Denpasar',
                'urgensi' => 'Medium',
                'sifat_project' => 'Non RBB',
                'deskripsi' => 'Renovasi dan upgrade fasilitas kantor cabang utama Denpasar',
                'pemilik_project_id' => $divUMA,
                'pic_proyek_id' => $pic3,
                'user_id' => $pic1,
                'status' => 'Progress',
            ],
            
            // 9. DIVISI MRO - Manajemen Risiko
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(40),
                'target_implementasi' => 'Q4 2025',
                'nama_project' => 'Sistem Monitoring Risiko Terintegrasi',
                'urgensi' => 'Very High',
                'sifat_project' => 'RBB',
                'deskripsi' => 'Implementasi sistem monitoring risiko kredit, operasional, dan pasar secara real-time',
                'pemilik_project_id' => $divMRO,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic1,
                'status' => 'Progress',
            ],
            
            // 10. DIVISI KRK - Kredit Ritel & Konsumer
            [
                'tanggal_inisiasi' => Carbon::now()->subDays(18),
                'target_implementasi' => 'Q1 2026',
                'nama_project' => 'Kredit Tanpa Agunan Online',
                'urgensi' => 'High',
                'sifat_project' => 'RBB',
                'deskripsi' => 'Pengembangan sistem pengajuan KTA online dengan approval otomatis berbasis scoring',
                'pemilik_project_id' => $divKRK,
                'pic_proyek_id' => $pic2,
                'user_id' => $pic2,
                'status' => 'Progress',
            ],
        ];

        foreach ($projects as $project) {
            Project::create($project);
        }
        
        $this->command->info('✅ ' . count($projects) . ' sample projects BPD Bali berhasil ditambahkan!');
    }
}
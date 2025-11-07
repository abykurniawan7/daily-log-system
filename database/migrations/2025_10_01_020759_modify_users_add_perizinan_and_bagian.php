<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Modifikasi kolom role - tambah 'perizinan'
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('karyawan', 'supervisi', 'perizinan') DEFAULT 'karyawan'");
        
        // Tambah kolom bagian setelah role
        Schema::table('users', function (Blueprint $table) {
            $table->enum('bagian', ['PGB', 'PKJ'])->nullable()->after('role');
        });
    }

    public function down(): void
    {
        // Kembalikan seperti semula kalau rollback
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('karyawan', 'supervisi') DEFAULT 'karyawan'");
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('bagian');
        });
    }
};
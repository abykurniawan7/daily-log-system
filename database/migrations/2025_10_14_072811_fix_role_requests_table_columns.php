<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('role_requests', function (Blueprint $table) {
            // Tambah 'approved_at' jika belum ada
            if (!Schema::hasColumn('role_requests', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('reviewed_at');
            }
            
            // Tambah 'approved_by' jika belum ada
            if (!Schema::hasColumn('role_requests', 'approved_by')) {
                $table->foreignId('approved_by')->nullable()
                    ->after('reviewed_by');
            }
            
            // Tambah 'dokumen_path' untuk upload dokumen jika belum ada
            if (!Schema::hasColumn('role_requests', 'dokumen_path')) {
                $table->string('dokumen_path')->nullable();
            }
        });
        
        // Rename 'reason' ke 'deskripsi' jika column 'reason' ada dan 'deskripsi' belum ada
        if (Schema::hasColumn('role_requests', 'reason') && !Schema::hasColumn('role_requests', 'deskripsi')) {
            Schema::table('role_requests', function (Blueprint $table) {
                $table->renameColumn('reason', 'deskripsi');
            });
        }
    }

    public function down(): void
    {
        Schema::table('role_requests', function (Blueprint $table) {
            if (Schema::hasColumn('role_requests', 'approved_at')) {
                $table->dropColumn('approved_at');
            }
            if (Schema::hasColumn('role_requests', 'approved_by')) {
                $table->dropColumn('approved_by');
            }
            if (Schema::hasColumn('role_requests', 'dokumen_path')) {
                $table->dropColumn('dokumen_path');
            }
        });
    }
};
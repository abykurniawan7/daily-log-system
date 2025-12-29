<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            // Hapus kolom email lama jika ada
            if (Schema::hasColumn('password_reset_requests', 'email')) {
                $table->dropColumn('email');
            }
            
            // Tambah kolom encrypted_email jika belum ada
            if (!Schema::hasColumn('password_reset_requests', 'encrypted_email')) {
                $table->text('encrypted_email')->after('user_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->dropColumn('encrypted_email');
            $table->string('email')->after('user_id');
        });
    }
};
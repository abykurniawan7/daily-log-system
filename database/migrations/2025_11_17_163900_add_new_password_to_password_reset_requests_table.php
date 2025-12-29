<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            // Tambah kolom new_password dan admin_response jika belum ada
            if (!Schema::hasColumn('password_reset_requests', 'new_password')) {
                $table->string('new_password')->nullable()->after('processed_at');
            }
            
            if (!Schema::hasColumn('password_reset_requests', 'admin_response')) {
                $table->text('admin_response')->nullable()->after('new_password');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->dropColumn(['new_password', 'admin_response']);
        });
    }
};
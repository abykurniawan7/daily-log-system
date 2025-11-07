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
        Schema::create('role_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('requested_role', ['supervisi', 'perizinan', 'karyawan']);
            $table->enum('requested_bagian', ['PGB', 'PKJ'])->nullable();
            $table->text('deskripsi'); // ✅ PENTING: field untuk alasan pengajuan
            $table->string('dokumen_path')->nullable(); // ✅ untuk upload dokumen
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            
            // ✅ Admin review fields
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            // ✅ Notes fields
            $table->text('rejection_reason')->nullable();
            $table->text('admin_notes')->nullable(); // ✅ INI YANG KURANG!
            
            $table->timestamps();

            // Index untuk performa
            $table->index(['user_id', 'status']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('role_requests');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_inisiasi');
            $table->string('target_implementasi'); // Format: "Q1 2024", "Q2 2025", dll
            $table->string('nama_project');
            $table->enum('urgensi', ['Low', 'Medium', 'High', 'Very High'])->default('Medium');
            $table->string('sifat_project'); // RBB/Non RBB, Regulator/Kedinasan, TL Audit
            $table->text('deskripsi')->nullable();
            
            // Foreign Keys
            $table->foreignId('pemilik_project_id')->constrained('divisions')->onDelete('cascade');
            $table->foreignId('pic_proyek_id')->constrained('users')->onDelete('cascade');
            
            $table->enum('status', ['Progress', 'Pending', 'Done'])->default('Progress');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
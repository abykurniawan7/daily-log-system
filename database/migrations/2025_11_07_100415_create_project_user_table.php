<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Create pivot table for many-to-many relationship
     * between projects and users (PICs)
     */
    public function up(): void
    {
        Schema::create('project_user', function (Blueprint $table) {
            $table->id();
            
            // Foreign keys
            $table->foreignId('project_id')
                  ->constrained('projects')
                  ->onDelete('cascade'); // Jika project dihapus, hapus juga relasi
            
            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); // Jika user dihapus, hapus juga relasi
            
            // Role type: pic (PIC utama) atau contributor (helper)
            $table->enum('role_type', ['pic', 'contributor'])
                  ->default('pic');
            
            $table->timestamps();
            
            // Prevent duplicate: 1 user tidak bisa jadi PIC 2x di project yang sama
            $table->unique(['project_id', 'user_id']);
            
            // Index untuk performance query
            $table->index('project_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     * 
     * Drop pivot table
     */
    public function down(): void
    {
        Schema::dropIfExists('project_user');
    }
};
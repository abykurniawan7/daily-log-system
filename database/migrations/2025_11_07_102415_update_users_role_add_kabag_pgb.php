<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Add 'kabag_pgb' role to users table ENUM
     * Update existing 'supervisi' users with bagian='PGB' to 'kabag_pgb'
     */
    public function up(): void
    {
        // ⚠️ IMPORTANT: Disable foreign key checks
        // Karena kita akan ALTER table yang punya foreign keys
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        
        // Step 1: Alter ENUM role - tambah 'kabag_pgb'
        DB::statement("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM(
                'supervisi', 
                'kabag_pgb', 
                'perizinan', 
                'karyawan', 
                'admin', 
                'guest'
            ) DEFAULT 'karyawan'
        ");
        
        // Step 2: Update existing supervisi dengan bagian PGB → jadi kabag_pgb
        $updated = DB::table('users')
            ->where('role', 'supervisi')
            ->where('bagian', 'PGB')
            ->update([
                'role' => 'kabag_pgb',
                'updated_at' => now()
            ]);
        
        // Step 3: Re-enable foreign key checks
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
        
        // Log hasil
        echo "\n✅ Updated {$updated} supervisi users to kabag_pgb\n";
    }

    /**
     * Reverse the migrations.
     * 
     * Rollback: kabag_pgb → supervisi
     */
    public function down(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        
        // Step 1: Rollback kabag_pgb → supervisi
        DB::table('users')
            ->where('role', 'kabag_pgb')
            ->update([
                'role' => 'supervisi',
                'updated_at' => now()
            ]);
        
        // Step 2: Remove 'kabag_pgb' from ENUM
        DB::statement("
            ALTER TABLE users 
            MODIFY COLUMN role ENUM(
                'supervisi', 
                'perizinan', 
                'karyawan', 
                'admin', 
                'guest'
            ) DEFAULT 'karyawan'
        ");
        
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
        
        echo "\n⚠️  Rolled back kabag_pgb to supervisi\n";
    }
};
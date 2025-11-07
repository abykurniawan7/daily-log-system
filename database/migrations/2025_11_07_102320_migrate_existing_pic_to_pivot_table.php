<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Migrate existing PIC data from projects.pic_proyek_id
     * to project_user pivot table
     */
    public function up(): void
    {
        // Ambil semua project yang punya PIC (pic_proyek_id tidak null)
        $projects = DB::table('projects')
            ->whereNotNull('pic_proyek_id')
            ->select('id', 'pic_proyek_id')
            ->get();
        
        // Loop dan insert ke pivot table
        foreach ($projects as $project) {
            // Cek apakah user masih ada (untuk safety)
            $userExists = DB::table('users')
                ->where('id', $project->pic_proyek_id)
                ->exists();
            
            if ($userExists) {
                // Insert ke pivot table
                DB::table('project_user')->insert([
                    'project_id' => $project->id,
                    'user_id' => $project->pic_proyek_id,
                    'role_type' => 'pic',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        
        // Log hasil migration
        $migratedCount = DB::table('project_user')->count();
        echo "\n✅ Migrated {$migratedCount} PIC relationships to pivot table\n";
    }

    /**
     * Reverse the migrations.
     * 
     * Clear pivot table (data bisa di-restore dari pic_proyek_id)
     */
    public function down(): void
    {
        DB::table('project_user')->truncate();
        echo "\n⚠️  Pivot table cleared. Data still exists in projects.pic_proyek_id\n";
    }
};
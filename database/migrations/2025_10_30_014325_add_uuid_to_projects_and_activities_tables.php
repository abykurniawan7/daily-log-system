<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ========== 1. ADD UUID COLUMN TO PROJECTS ==========
        Schema::table('projects', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->unique('uuid');
        });
        
        // Generate UUID untuk semua projects yang sudah ada
        $projects = DB::table('projects')->whereNull('uuid')->get();
        foreach ($projects as $project) {
            DB::table('projects')
                ->where('id', $project->id)
                ->update(['uuid' => (string) Str::uuid()]);
        }
        
        // Make uuid NOT NULL setelah semua terisi
        Schema::table('projects', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
        });
        
        // ========== 2. ADD UUID COLUMN TO ACTIVITIES ==========
        Schema::table('activities', function (Blueprint $table) {
            $table->uuid('uuid')->nullable()->after('id');
            $table->uuid('project_uuid')->nullable()->after('project_id');
            $table->unique('uuid');
        });
        
        // Generate UUID untuk semua activities dan link ke project UUID
        $activities = DB::table('activities')->whereNull('uuid')->get();
        foreach ($activities as $activity) {
            $projectUuid = DB::table('projects')
                ->where('id', $activity->project_id)
                ->value('uuid');
            
            DB::table('activities')
                ->where('id', $activity->id)
                ->update([
                    'uuid' => (string) Str::uuid(),
                    'project_uuid' => $projectUuid
                ]);
        }
        
        // Make NOT NULL
        Schema::table('activities', function (Blueprint $table) {
            $table->uuid('uuid')->nullable(false)->change();
            $table->uuid('project_uuid')->nullable(false)->change();
        });
        
        // Add index untuk performance
        Schema::table('activities', function (Blueprint $table) {
            $table->index('project_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['project_uuid']);
            $table->dropUnique(['uuid']);
            $table->dropColumn(['uuid', 'project_uuid']);
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['uuid']);
            $table->dropColumn('uuid');
        });
    }
};
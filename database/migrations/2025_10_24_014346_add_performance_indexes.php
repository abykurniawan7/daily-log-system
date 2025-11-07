<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Check if index exists on a table
     */
    private function indexExists($table, $indexName): bool
    {
        $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = '{$indexName}'");
        return !empty($indexes);
    }

    /**
     * Add index if it doesn't exist
     */
    private function addIndexIfNotExists($table, $columns, $indexName): void
    {
        if (!$this->indexExists($table, $indexName)) {
            Schema::table($table, function (Blueprint $blueprint) use ($columns, $indexName) {
                if (is_array($columns)) {
                    $blueprint->index($columns, $indexName);
                } else {
                    $blueprint->index($columns, $indexName);
                }
            });
            echo "   ✅ Created index: {$indexName}\n";
        } else {
            echo "   ⏭️  Skipped (exists): {$indexName}\n";
        }
    }

    public function up(): void
    {
        echo "\n🔍 Checking and creating indexes...\n\n";
        
        // Activities table indexes
        echo "📋 Activities table:\n";
        $this->addIndexIfNotExists('activities', 'user_id', 'idx_activities_user_id');
        $this->addIndexIfNotExists('activities', 'project_id', 'idx_activities_project_id');
        $this->addIndexIfNotExists('activities', 'status', 'idx_activities_status');
        $this->addIndexIfNotExists('activities', 'created_at', 'idx_activities_created_at');
        $this->addIndexIfNotExists('activities', ['user_id', 'created_at'], 'idx_activities_user_created');
        
        // Projects table indexes
        echo "\n📁 Projects table:\n";
        $this->addIndexIfNotExists('projects', 'pic_proyek_id', 'idx_projects_pic');
        $this->addIndexIfNotExists('projects', 'pemilik_project_id', 'idx_projects_owner');
        $this->addIndexIfNotExists('projects', 'status', 'idx_projects_status');
        $this->addIndexIfNotExists('projects', 'user_id', 'idx_projects_user_id');
        
        // Activity Logs table indexes (if exists)
        if (Schema::hasTable('activity_logs')) {
            echo "\n📝 Activity Logs table:\n";
            $this->addIndexIfNotExists('activity_logs', 'user_id', 'idx_logs_user_id');
            $this->addIndexIfNotExists('activity_logs', 'action', 'idx_logs_action');
            $this->addIndexIfNotExists('activity_logs', 'created_at', 'idx_logs_created_at');
            $this->addIndexIfNotExists('activity_logs', ['user_id', 'created_at'], 'idx_logs_user_created');
        }
        
        // Sessions table index
        if (Schema::hasTable('sessions')) {
            echo "\n🔐 Sessions table:\n";
            $this->addIndexIfNotExists('sessions', 'last_activity', 'idx_sessions_last_activity');
        }
        
        echo "\n";
    }

    public function down(): void
    {
        // Drop indexes if they exist
        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex(['idx_activities_user_id']);
            $table->dropIndex(['idx_activities_project_id']);
            $table->dropIndex(['idx_activities_status']);
            $table->dropIndex(['idx_activities_created_at']);
            $table->dropIndex(['idx_activities_user_created']);
        });
        
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex(['idx_projects_pic']);
            $table->dropIndex(['idx_projects_owner']);
            $table->dropIndex(['idx_projects_status']);
            $table->dropIndex(['idx_projects_user_id']);
        });
        
        if (Schema::hasTable('activity_logs')) {
            Schema::table('activity_logs', function (Blueprint $table) {
                $table->dropIndex(['idx_logs_user_id']);
                $table->dropIndex(['idx_logs_action']);
                $table->dropIndex(['idx_logs_created_at']);
                $table->dropIndex(['idx_logs_user_created']);
            });
        }
        
        if (Schema::hasTable('sessions')) {
            Schema::table('sessions', function (Blueprint $table) {
                $table->dropIndex(['idx_sessions_last_activity']);
            });
        }
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Index untuk Projects table
        Schema::table('projects', function (Blueprint $table) {
            $table->index('pemilik_project_id', 'idx_projects_pemilik');
            $table->index('pic_proyek_id', 'idx_projects_pic');
            $table->index('status', 'idx_projects_status');
            $table->index('urgensi', 'idx_projects_urgensi');
            $table->index(['pic_proyek_id', 'status'], 'idx_projects_pic_status');
        });

        // Index untuk Activities table
        Schema::table('activities', function (Blueprint $table) {
            $table->index('user_id', 'idx_activities_user');
            $table->index('project_id', 'idx_activities_project');
            $table->index('status', 'idx_activities_status');
            $table->index('tanggal_mulai', 'idx_activities_tanggal');
            $table->index(['user_id', 'status'], 'idx_activities_user_status');
        });

        // Index untuk Users table
        Schema::table('users', function (Blueprint $table) {
            $table->index('role', 'idx_users_role');
            $table->index('bagian', 'idx_users_bagian');
            $table->index(['role', 'bagian'], 'idx_users_role_bagian');
        });
    }

    public function down()
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropIndex('idx_projects_pemilik');
            $table->dropIndex('idx_projects_pic');
            $table->dropIndex('idx_projects_status');
            $table->dropIndex('idx_projects_urgensi');
            $table->dropIndex('idx_projects_pic_status');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->dropIndex('idx_activities_user');
            $table->dropIndex('idx_activities_project');
            $table->dropIndex('idx_activities_status');
            $table->dropIndex('idx_activities_tanggal');
            $table->dropIndex('idx_activities_user_status');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropIndex('idx_users_role');
            $table->dropIndex('idx_users_bagian');
            $table->dropIndex('idx_users_role_bagian');
        });
    }
};
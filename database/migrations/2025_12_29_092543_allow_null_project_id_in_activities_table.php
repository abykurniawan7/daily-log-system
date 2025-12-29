<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Ubah project_id jadi nullable
            $table->unsignedBigInteger('project_id')->nullable()->change();
            
            // ✅ TAMBAHAN: Ubah project_uuid jadi nullable juga
            $table->string('project_uuid', 36)->nullable()->change();
            
            // Tambah kolom placeholder_project_name (kalau belum ada)
            if (!Schema::hasColumn('activities', 'placeholder_project_name')) {
                $table->string('placeholder_project_name', 255)->nullable()->after('project_id');
            }
        });
    }

    public function down()
    {
        Schema::table('activities', function (Blueprint $table) {
            // Kembalikan ke NOT NULL
            $table->unsignedBigInteger('project_id')->nullable(false)->change();
            $table->string('project_uuid', 36)->nullable(false)->change();
            
            // Drop kolom placeholder
            if (Schema::hasColumn('activities', 'placeholder_project_name')) {
                $table->dropColumn('placeholder_project_name');
            }
        });
    }
};
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
        Schema::table('projects', function (Blueprint $table) {
            // Add pengawas_id column after pic_proyek_id
            $table->foreignId('pengawas_id')
                  ->nullable()
                  ->after('pic_proyek_id')
                  ->constrained('users')
                  ->onDelete('set null')
                  ->comment('Pengawas/Supervisor Project (usually Kadiv)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['pengawas_id']);
            $table->dropColumn('pengawas_id');
        });
    }
};
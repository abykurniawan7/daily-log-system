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
        Schema::table('password_reset_requests', function (Blueprint $table) {
            // Rename kolom encrypted_email menjadi email
            $table->renameColumn('encrypted_email', 'email');
        });
        
        Schema::table('password_reset_requests', function (Blueprint $table) {
            // Ubah tipe dari TEXT menjadi STRING dan buat nullable
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->text('encrypted_email')->nullable(false)->change();
        });
        
        Schema::table('password_reset_requests', function (Blueprint $table) {
            $table->renameColumn('email', 'encrypted_email');
        });
    }
};
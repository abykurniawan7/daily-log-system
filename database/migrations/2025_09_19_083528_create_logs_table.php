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
        Schema::create('logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_input');
            $table->date('due_date')->nullable();
            $table->string('nama_project');
            $table->enum('urgency', ['High', 'Medium', 'Low']);
            $table->enum('sifat_pekerjaan', ['Regulator', 'Non-Regulator']);
            $table->text('deskripsi');
            $table->string('pemilik_project'); // misalnya DJA, OKA
            $table->enum('jenis_kegiatan', ['meeting','coding','dokumentasi','support']);
            $table->enum('status', ['Progress','Done','Pending','Review']);
            $table->string('lampiran')->nullable(); // path file upload
            $table->boolean('locked')->default(false);
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('logs');
    }
};

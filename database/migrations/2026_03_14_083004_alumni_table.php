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
        Schema::create('alumni', function (Blueprint $table) {
            $table->id();
            $table->string('nim')->unique();
            $table->string('nama_lengkap');
            $table->string('program_studi');
            $table->year('tahun_lulus');
            $table->string('email')->nullable();
            $table->string('kota')->nullable();
            $table->enum('status_pelacakan', [
                'Belum Dilacak',
                'Teridentifikasi',
                'Perlu Verifikasi',
                'Tidak Ditemukan'
            ])->default('Belum Dilacak');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni');
    }
};
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
        Schema::create('tracking_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumni_id')->constrained('alumni')->onDelete('cascade');
            $table->foreignId('tracking_source_id')->constrained('tracking_sources')->onDelete('cascade');

            $table->string('query')->nullable();
            $table->string('judul')->nullable();
            $table->text('snippet')->nullable();
            $table->string('url')->nullable();

            $table->string('nama_terdeteksi')->nullable();
            $table->string('afiliasi')->nullable();
            $table->string('jabatan')->nullable();
            $table->string('lokasi')->nullable();

            $table->decimal('confidence_score', 5, 2)->default(0);
            $table->enum('status_verifikasi', [
                'Kemungkinan Kuat',
                'Perlu Verifikasi',
                'Tidak Cocok'
            ])->default('Perlu Verifikasi');

            $table->timestamp('tanggal_ditemukan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_results');
    }
};
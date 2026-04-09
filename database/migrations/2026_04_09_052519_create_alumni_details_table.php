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
        Schema::create('alumni_details', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nim')->nullable();
            $table->string('nama_pt')->nullable();
            $table->string('kode_pt')->nullable();
            $table->string('prodi')->nullable();
            $table->string('kode_prodi')->nullable();
            $table->string('jenjang')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('status')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alumni_details');
    }
};

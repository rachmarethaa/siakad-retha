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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('krs_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('jadwal_mengajar_id');
            $table->unsignedBigInteger('dosen_id');
            $table->date('tanggal');
            $table->enum('status', ['hadir', 'absen', 'izin', 'sakit'])->default('absen');
            $table->time('jam_masuk')->nullable(); // Waktu mahasiswa hadir
            $table->time('jam_keluar')->nullable(); // Waktu mahasiswa pulang
            $table->text('keterangan')->nullable();
            $table->boolean('sudah_dikonfirmasi')->default(false); // Flag untuk validasi dosen
            $table->timestamps();
            $table->softDeletes(); // Support for soft deletes

            // Foreign keys
            $table->foreign('krs_id')->references('id')->on('krs')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('jadwal_mengajar_id')->references('id')->on('jadwal_mengajar')->onDelete('cascade');
            $table->foreign('dosen_id')->references('id')->on('dosen')->onDelete('cascade');

            // Indexes
            $table->index('mahasiswa_id');
            $table->index('jadwal_mengajar_id');
            $table->index('dosen_id');
            $table->index('tanggal');
            $table->index('status');
            $table->index('sudah_dikonfirmasi');

            // Unique constraint untuk prevent duplicate entries
            $table->unique(['krs_id', 'tanggal']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};

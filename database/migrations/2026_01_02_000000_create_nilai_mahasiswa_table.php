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
        Schema::create('nilai_mahasiswa', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jadwal_mengajar_id');
            $table->unsignedBigInteger('mahasiswa_id');
            $table->decimal('nilai_akhir', 5, 2)->nullable()->comment('Nilai akhir (0-100)');
            $table->decimal('nilai_tugas', 5, 2)->nullable()->comment('Nilai tugas/quiz');
            $table->decimal('nilai_uts', 5, 2)->nullable()->comment('Nilai UTS');
            $table->decimal('nilai_uas', 5, 2)->nullable()->comment('Nilai UAS');
            $table->string('grade', 2)->nullable()->comment('Grade (A, B, C, D, E)');
            $table->text('keterangan')->nullable()->comment('Catatan dosen');
            $table->timestamp('input_at')->nullable()->comment('Waktu input nilai');
            $table->timestamps();

            // Foreign keys
            $table->foreign('jadwal_mengajar_id')->references('id')->on('jadwal_mengajar')->onDelete('cascade');
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');

            // Unique constraint: satu mahasiswa hanya bisa punya satu nilai per jadwal
            $table->unique(['jadwal_mengajar_id', 'mahasiswa_id']);

            // Indexes untuk performa
            $table->index('jadwal_mengajar_id');
            $table->index('mahasiswa_id');
            $table->index('grade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilai_mahasiswa');
    }
};

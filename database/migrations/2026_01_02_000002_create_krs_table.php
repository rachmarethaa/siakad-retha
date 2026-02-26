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
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mahasiswa_id');
            $table->unsignedBigInteger('jadwal_mengajar_id');
            $table->string('status')->default('aktif')->comment('aktif, batal, selesai');
            $table->timestamp('diambil_at')->nullable()->comment('Waktu pengambilan KRS');
            $table->timestamps();

            // Foreign keys
            $table->foreign('mahasiswa_id')->references('id')->on('mahasiswa')->onDelete('cascade');
            $table->foreign('jadwal_mengajar_id')->references('id')->on('jadwal_mengajar')->onDelete('cascade');

            // Unique constraint: mahasiswa hanya bisa ambil satu KRS per jadwal
            $table->unique(['mahasiswa_id', 'jadwal_mengajar_id']);

            // Indexes
            $table->index('mahasiswa_id');
            $table->index('jadwal_mengajar_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};

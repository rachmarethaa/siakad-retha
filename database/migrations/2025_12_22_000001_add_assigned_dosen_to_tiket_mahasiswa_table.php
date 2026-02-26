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
        Schema::table('tiket_mahasiswa', function (Blueprint $table) {
            $table->unsignedBigInteger('assigned_dosen_id')->nullable()->after('assigned_staff_id');
            $table->foreign('assigned_dosen_id')->references('id')->on('dosen')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tiket_mahasiswa', function (Blueprint $table) {
            $table->dropForeign(['assigned_dosen_id']);
            $table->dropColumn('assigned_dosen_id');
        });
    }
};

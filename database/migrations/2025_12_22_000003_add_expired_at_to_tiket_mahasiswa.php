<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExpiredAtToTiketMahasiswa extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tiket_mahasiswa', function (Blueprint $table) {
            $table->timestamp('expired_at')->nullable()->after('sla_deadline');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('tiket_mahasiswa', function (Blueprint $table) {
            $table->dropColumn('expired_at');
        });
    }
}

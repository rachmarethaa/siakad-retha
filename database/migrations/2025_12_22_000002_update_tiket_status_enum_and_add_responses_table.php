<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateTiketStatusEnumAndAddResponsesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * We'll alter the `status` enum to include 'diteruskan' and create a
     * `tiket_responses` table to store threaded responses.
     *
     * Note: altering ENUMs in MySQL requires an explicit MODIFY statement.
     */
    public function up()
    {
        // alter enum to include 'diteruskan'
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `tiket_mahasiswa` MODIFY `status` ENUM('dibuka','diproses','diteruskan','ditanggapi') NOT NULL DEFAULT 'dibuka'");

        Schema::create('tiket_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tiket_mahasiswa_id');
            $table->string('sender_role')->comment('mahasiswa|staff|dosen');
            $table->unsignedBigInteger('sender_id')->nullable();
            $table->text('message');
            $table->timestamps();

            $table->foreign('tiket_mahasiswa_id')->references('id')->on('tiket_mahasiswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('tiket_responses');
        // revert enum to previous set (note: data using 'diteruskan' may break on down)
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE `tiket_mahasiswa` MODIFY `status` ENUM('dibuka','diproses','ditanggapi') NOT NULL DEFAULT 'dibuka'");
    }
}

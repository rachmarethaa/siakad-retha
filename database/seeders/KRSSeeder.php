<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\KRS;
use App\Models\Mahasiswa;
use App\Models\JadwalMengajar;

class KRSSeeder extends Seeder
{
    /**
     * Seed KRS (daftar kelas mahasiswa)
     */
    public function run(): void
    {
        $mahasiswas = Mahasiswa::all();
        $jadwals = JadwalMengajar::all();

        // Daftarkan setiap mahasiswa ke setiap jadwal (untuk testing)
        foreach ($mahasiswas as $m) {
            foreach ($jadwals as $j) {
                KRS::firstOrCreate(
                    [
                        'mahasiswa_id' => $m->id,
                        'jadwal_mengajar_id' => $j->id,
                    ],
                    [
                        'status' => 'aktif',
                    ]
                );
            }
        }
    }
}

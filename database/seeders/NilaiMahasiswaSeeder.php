<?php

namespace Database\Seeders;

use App\Models\NilaiMahasiswa;
use App\Models\JadwalMengajar;
use App\Models\Mahasiswa;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class NilaiMahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua jadwal mengajar
        $jadwals = JadwalMengajar::all();
        
        foreach ($jadwals as $jadwal) {
            // Ambil semua mahasiswa dengan prodi yang sesuai
            $mahasiswas = Mahasiswa::where('prodi', $jadwal->prodi)->get();
            
            foreach ($mahasiswas as $mahasiswa) {
                // Cek apakah sudah ada nilai
                $existing = NilaiMahasiswa::where([
                    'jadwal_mengajar_id' => $jadwal->id,
                    'mahasiswa_id' => $mahasiswa->id
                ])->first();

                if (!$existing) {
                    // Generate random nilai for testing
                    $nilai_tugas = rand(70, 95);
                    $nilai_uts = rand(65, 90);
                    $nilai_uas = rand(60, 95);
                    
                    $nilai_akhir = (0.30 * $nilai_tugas) + (0.30 * $nilai_uts) + (0.40 * $nilai_uas);
                    $grade = $this->hitungGrade($nilai_akhir);

                    NilaiMahasiswa::create([
                        'jadwal_mengajar_id' => $jadwal->id,
                        'mahasiswa_id' => $mahasiswa->id,
                        'nilai_tugas' => $nilai_tugas,
                        'nilai_uts' => $nilai_uts,
                        'nilai_uas' => $nilai_uas,
                        'nilai_akhir' => $nilai_akhir,
                        'grade' => $grade,
                        'keterangan' => 'Data seeder untuk testing',
                        'input_at' => now()
                    ]);
                }
            }
        }
    }

    private function hitungGrade($nilai)
    {
        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 65) return 'C';
        if ($nilai >= 55) return 'D';
        return 'E';
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Staff;
use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\JadwalMengajar;
use Illuminate\Support\Facades\Hash;

class SiakadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Seed Staff
        Staff::create([
            'name' => 'Admin Staff',
            'email' => 'admin@siakad.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        Staff::create([
            'name' => 'Staff Support',
            'email' => 'staff@siakad.test',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);

        // Seed Dosen
        $dosen1 = Dosen::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@dosen.test',
            'nip' => '1987010001',
            'password' => Hash::make('password'),
        ]);

        $dosen2 = Dosen::create([
            'name' => 'Prof. Siti Nurhaliza',
            'email' => 'siti@dosen.test',
            'nip' => '1990020002',
            'password' => Hash::make('password'),
        ]);

        $dosen3 = Dosen::create([
            'name' => 'Ir. Ahmad Wijaya',
            'email' => 'ahmad@dosen.test',
            'nip' => '1988030003',
            'password' => Hash::make('password'),
        ]);

        // Seed Mahasiswa
        Mahasiswa::create([
            'name' => 'Andi Kusuma',
            'email' => 'andi@mhs.test',
            'nim' => '2024001001',
            'password' => Hash::make('password'),
        ]);

        Mahasiswa::create([
            'name' => 'Dewi Wijaya',
            'email' => 'dewi@mhs.test',
            'nim' => '2024001002',
            'password' => Hash::make('password'),
        ]);

        Mahasiswa::create([
            'name' => 'Riko Pratama',
            'email' => 'riko@mhs.test',
            'nim' => '2024001003',
            'password' => Hash::make('password'),
        ]);

        // Seed Jadwal Mengajar
        JadwalMengajar::create([
            'dosen_id' => $dosen1->id,
            'kode_mk' => 'CS101',
            'mata_kuliah' => 'Pemrograman Web',
            'prodi' => 'rks',
            'ruang' => 'Lab 1',
            'hari' => 'Senin',
            'jam_mulai' => '08:00',
            'jam_selesai' => '10:00',
            'semester' => '3',
        ]);

        JadwalMengajar::create([
            'dosen_id' => $dosen2->id,
            'kode_mk' => 'MATH201',
            'mata_kuliah' => 'Kalkulus Lanjutan',
            'prodi' => 'bd',
            'ruang' => 'Ruang 301',
            'hari' => 'Selasa',
            'jam_mulai' => '09:00',
            'jam_selesai' => '11:00',
            'semester' => '3',
        ]);

        JadwalMengajar::create([
            'dosen_id' => $dosen3->id,
            'kode_mk' => 'EN102',
            'mata_kuliah' => 'Bahasa Inggris Teknis',
            'prodi' => 'trm',
            'ruang' => 'Ruang 205',
            'hari' => 'Rabu',
            'jam_mulai' => '13:00',
            'jam_selesai' => '15:00',
            'semester' => '3',
        ]);
    }
}

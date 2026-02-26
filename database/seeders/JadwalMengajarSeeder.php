<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\JadwalMengajar;

class JadwalMengajarSeeder extends Seeder
{
    public function run(): void
    {
        // Create sample jadwal (ensure Dosen seeder ran first)
        JadwalMengajar::factory()->count(30)->create();
    }
}

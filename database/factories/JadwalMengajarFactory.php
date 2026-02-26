<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\JadwalMengajar;
use App\Models\Dosen;

class JadwalMengajarFactory extends Factory
{
    protected $model = JadwalMengajar::class;

    public function definition()
    {
        $hari = $this->faker->randomElement(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
        $start = $this->faker->time('H:i');
        $end = date('H:i', strtotime($start) + 60*90);
        $dosenIds = Dosen::pluck('id')->toArray();

        return [
            'dosen_id' => $this->faker->optional()->randomElement($dosenIds),
            'kode_mk' => strtoupper($this->faker->bothify('MK###')),
            'mata_kuliah' => $this->faker->sentence(3),
            'prodi' => $this->faker->randomElement(['TI','SI','RPL','CE']),
            'ruang' => $this->faker->randomElement(['R101','R102','Lab1','Lab2']),
            'hari' => $hari,
            'jam_mulai' => $start,
            'jam_selesai' => $end,
            'semester' => '2025/1',
        ];
    }
}

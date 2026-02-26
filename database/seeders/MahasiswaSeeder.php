<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Mahasiswa;
use Illuminate\Support\Facades\Hash;

class MahasiswaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Mahasiswa::create([
            'name' => 'Rachmaretha Rayvana',
            'email' => 'rachmaretha@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'nim' => '2022001001',
            'prodi' => 'rks',
        ]);

        Mahasiswa::create([
            'name' => 'Noka Maharani',
            'email' => 'noka@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'nim' => '2022001002',
            'prodi' => 'bd',
        ]);
    }
}

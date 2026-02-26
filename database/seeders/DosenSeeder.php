<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Dosen;
use Illuminate\Support\Facades\Hash;

class DosenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Dosen::create([
            'name' => 'Icha Nuha Nugraha S.Kom, M.Kom',
            'email' => 'icha.nuha@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'nip' => '198501011987121001',
        ]);

        Dosen::create([
            'name' => 'Ir. Supriyadi, M.T.',
            'email' => 'supriyadi@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'nip' => '198602021988032002',
        ]);
    }
}

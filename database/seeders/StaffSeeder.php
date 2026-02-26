<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;

class StaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default admin staff
        Staff::create([
            'name' => 'Admin Staff',
            'email' => 'admin@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        // Create default staff
        Staff::create([
            'name' => 'Staff User',
            'email' => 'staff@bhaktisemesta.ac.id',
            'password' => Hash::make('password'),
            'role' => 'staff',
        ]);
    }
}

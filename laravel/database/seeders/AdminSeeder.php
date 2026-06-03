<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Admin::create([
            'name'      => 'Super Admin',
            'email'     => 'admin@presensi.id',
            'password'  => Hash::make('password123'),
            'role'      => 'superadmin',
            'is_active' => true,
        ]);

        Admin::create([
            'name'      => 'Operator Kelas',
            'email'     => 'operator@presensi.id',
            'password'  => Hash::make('operator123'),
            'role'      => 'operator',
            'is_active' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\ClassRoom;
use Illuminate\Database\Seeder;

class ClassSeeder extends Seeder
{
    public function run(): void
    {
        $classes = [
            [
                'name'          => 'Teknik Informatika A',
                'code'          => 'TI-A-2024',
                'department'    => 'Teknik Informatika',
                'semester'      => 3,
                'academic_year' => 2024,
            ],
            [
                'name'          => 'Teknik Informatika B',
                'code'          => 'TI-B-2024',
                'department'    => 'Teknik Informatika',
                'semester'      => 3,
                'academic_year' => 2024,
            ],
            [
                'name'          => 'Sistem Informasi A',
                'code'          => 'SI-A-2024',
                'department'    => 'Sistem Informasi',
                'semester'      => 5,
                'academic_year' => 2024,
            ],
        ];

        foreach ($classes as $class) {
            ClassRoom::create($class);
        }
    }
}

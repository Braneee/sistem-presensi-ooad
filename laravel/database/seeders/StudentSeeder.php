<?php

namespace Database\Seeders;

use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            // Kelas TI-A (class_id: 1)
            ['nim' => '2024001', 'name' => 'Andi Firmansyah',    'email' => 'andi.firmansyah@mhs.id',    'class_id' => 1, 'gender' => 'L', 'phone' => '08111111001'],
            ['nim' => '2024002', 'name' => 'Budi Santoso',       'email' => 'budi.santoso@mhs.id',       'class_id' => 1, 'gender' => 'L', 'phone' => '08111111002'],
            ['nim' => '2024003', 'name' => 'Citra Dewi Lestari', 'email' => 'citra.dewi@mhs.id',         'class_id' => 1, 'gender' => 'P', 'phone' => '08111111003'],
            ['nim' => '2024004', 'name' => 'Dian Ayu Rahayu',    'email' => 'dian.ayu@mhs.id',           'class_id' => 1, 'gender' => 'P', 'phone' => '08111111004'],
            ['nim' => '2024005', 'name' => 'Eko Prasetyo Wibowo','email' => 'eko.prasetyo@mhs.id',       'class_id' => 1, 'gender' => 'L', 'phone' => '08111111005'],

            // Kelas TI-B (class_id: 2)
            ['nim' => '2024006', 'name' => 'Farah Nadia Putri',  'email' => 'farah.nadia@mhs.id',        'class_id' => 2, 'gender' => 'P', 'phone' => '08111111006'],
            ['nim' => '2024007', 'name' => 'Gilang Ramadhan',    'email' => 'gilang.ramadhan@mhs.id',    'class_id' => 2, 'gender' => 'L', 'phone' => '08111111007'],
            ['nim' => '2024008', 'name' => 'Hani Safitri',       'email' => 'hani.safitri@mhs.id',       'class_id' => 2, 'gender' => 'P', 'phone' => '08111111008'],
            ['nim' => '2024009', 'name' => 'Irfan Maulana',      'email' => 'irfan.maulana@mhs.id',      'class_id' => 2, 'gender' => 'L', 'phone' => '08111111009'],
            ['nim' => '2024010', 'name' => 'Jessica Amalia',     'email' => 'jessica.amalia@mhs.id',     'class_id' => 2, 'gender' => 'P', 'phone' => '08111111010'],

            // Kelas SI-A (class_id: 3)
            ['nim' => '2024011', 'name' => 'Kevin Pratama',      'email' => 'kevin.pratama@mhs.id',      'class_id' => 3, 'gender' => 'L', 'phone' => '08111111011'],
            ['nim' => '2024012', 'name' => 'Layla Indah Sari',   'email' => 'layla.indah@mhs.id',        'class_id' => 3, 'gender' => 'P', 'phone' => '08111111012'],
            ['nim' => '2024013', 'name' => 'Muhammad Ridwan',    'email' => 'm.ridwan@mhs.id',           'class_id' => 3, 'gender' => 'L', 'phone' => '08111111013'],
            ['nim' => '2024014', 'name' => 'Nadia Kusuma',       'email' => 'nadia.kusuma@mhs.id',       'class_id' => 3, 'gender' => 'P', 'phone' => '08111111014'],
            ['nim' => '2024015', 'name' => 'Omar Abdullah',      'email' => 'omar.abdullah@mhs.id',      'class_id' => 3, 'gender' => 'L', 'phone' => '08111111015'],
        ];

        foreach ($students as $student) {
            Student::create($student);
        }
    }
}

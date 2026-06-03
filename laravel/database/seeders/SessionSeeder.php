<?php

namespace Database\Seeders;

use App\Models\Session;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SessionSeeder extends Seeder
{
    public function run(): void
    {
        $today = now()->format('Y-m-d');

        Session::create([
            'title'                  => 'Pemrograman Web - Pertemuan 1',
            'code'                   => 'SES-' . strtoupper(Str::random(6)),
            'class_id'               => 1,
            'created_by'             => 1,
            'date'                   => $today,
            'start_time'             => '08:00',
            'end_time'               => '10:00',
            'status'                 => 'open',
            'notes'                  => 'Pertemuan pertama Pemrograman Web',
            'late_threshold_minutes' => 15,
        ]);

        Session::create([
            'title'                  => 'Basis Data - Pertemuan 1',
            'code'                   => 'SES-' . strtoupper(Str::random(6)),
            'class_id'               => 2,
            'created_by'             => 1,
            'date'                   => $today,
            'start_time'             => '10:00',
            'end_time'               => '12:00',
            'status'                 => 'open',
            'notes'                  => 'Pertemuan pertama Basis Data',
            'late_threshold_minutes' => 15,
        ]);

        Session::create([
            'title'                  => 'Kecerdasan Buatan - Pertemuan 5',
            'code'                   => 'SES-' . strtoupper(Str::random(6)),
            'class_id'               => 3,
            'created_by'             => 1,
            'date'                   => now()->subDay()->format('Y-m-d'),
            'start_time'             => '13:00',
            'end_time'               => '15:00',
            'status'                 => 'closed',
            'notes'                  => null,
            'late_threshold_minutes' => 15,
        ]);
    }
}

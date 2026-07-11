<?php

namespace Database\Seeders;

use App\Models\PeriodePencatatan;
use Illuminate\Database\Seeder;

class PeriodePencatatanSeeder extends Seeder
{
    public function run(): void
    {
        PeriodePencatatan::create([
            'bulan'       => now()->month,
            'tahun'       => now()->year,
            'status'      => 'buka',
            'dibuka_oleh' => 1, // admin
            'dibuka_at'   => now(),
        ]);
    }
}
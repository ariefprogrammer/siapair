<?php

namespace Database\Seeders;

use App\Models\TarifProgresif;
use Illuminate\Database\Seeder;

class TarifProgresifSeeder extends Seeder
{
    public function run(): void
    {
        $tarifs = [
            [
                'tier'         => 1,
                'batas_bawah'  => 0,
                'batas_atas'   => 10,
                'harga_per_m3' => 1500,
                'keterangan'   => 'Pemakaian dasar (0–10 m³)',
                'is_active'    => true,
            ],
            [
                'tier'         => 2,
                'batas_bawah'  => 10,
                'batas_atas'   => 20,
                'harga_per_m3' => 2500,
                'keterangan'   => 'Pemakaian menengah (10–20 m³)',
                'is_active'    => true,
            ],
            [
                'tier'         => 3,
                'batas_bawah'  => 20,
                'batas_atas'   => null,
                'harga_per_m3' => 4000,
                'keterangan'   => 'Pemakaian tinggi (>20 m³)',
                'is_active'    => true,
            ],
        ];

        foreach ($tarifs as $tarif) {
            TarifProgresif::create($tarif);
        }
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TarifProgresif extends Model
{
    protected $table = 'tarif_progresif';

    protected $fillable = [
        'tier', 'batas_bawah', 'batas_atas',
        'harga_per_m3', 'keterangan', 'is_active',
    ];

    protected $casts = [
        'batas_bawah'  => 'float',
        'batas_atas'   => 'float',
        'harga_per_m3' => 'float',
        'is_active'    => 'boolean',
    ];

    public function scopeAktif($query)
    {
        return $query->where('is_active', true)->orderBy('tier');
    }

    /**
     * Hitung total biaya air dari total pemakaian (m³)
     * menggunakan tarif progresif aktif yang sudah di-load.
     *
     * Contoh penggunaan:
     *   $tarif = TarifProgresif::aktif()->get();
     *   [$total, $breakdown] = TarifProgresif::hitungBiaya(24, $tarif);
     */
    public static function hitungBiaya(float $pemakaian, $tarifs): array
    {
        $total     = 0;
        $breakdown = [];
        $sisa      = $pemakaian;

        foreach ($tarifs as $tarif) {
            if ($sisa <= 0) break;

            $kapasitas = $tarif->batas_atas !== null
                ? $tarif->batas_atas - $tarif->batas_bawah
                : $sisa; // tier terakhir: ambil semua sisa

            $pemakaianTier = min($sisa, $kapasitas);
            $subtotal      = $pemakaianTier * $tarif->harga_per_m3;

            $breakdown[] = [
                'tier'         => $tarif->tier,
                'batas_bawah'  => $tarif->batas_bawah,
                'batas_atas'   => $tarif->batas_atas,
                'pemakaian'    => $pemakaianTier,
                'harga_per_m3' => $tarif->harga_per_m3,
                'subtotal'     => $subtotal,
            ];

            $total += $subtotal;
            $sisa  -= $pemakaianTier;
        }

        return [$total, $breakdown];
    }
}
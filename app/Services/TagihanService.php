<?php

namespace App\Services;

use App\Models\CatatanMeter;
use App\Models\Tagihan;
use App\Models\TarifProgresif;
use App\Models\ConfigGeneral;
use Illuminate\Support\Facades\DB;

class TagihanService
{
    /**
     * Generate tagihan dari catatan meter.
     * Jika tagihan sudah ada untuk catatan ini, return yang existing.
     */
    public function generateDariCatatan(CatatanMeter $catatan): Tagihan
    {
        // Jika sudah ada tagihan, return langsung
        if ($catatan->sudahAdaTagihan()) {
            return $catatan->tagihan;
        }

        $tarifs = TarifProgresif::aktif()->get();

        if ($tarifs->isEmpty()) {
            throw new \RuntimeException('Belum ada tarif progresif aktif. Hubungi administrator.');
        }

        [$biayaAir, $breakdown] = TarifProgresif::hitungBiaya($catatan->pemakaian, $tarifs);

        $biayaAdmin   = $this->getBiayaAdmin();
        $biayaBeban   = $this->getBiayaBeban();
        $totalTagihan = $biayaAir + $biayaAdmin + $biayaBeban;

        return DB::transaction(function () use ($catatan, $biayaAir, $biayaAdmin, $biayaBeban, $totalTagihan, $breakdown) {
            return Tagihan::create([
                'pelanggan_id'    => $catatan->pelanggan_id,
                'catatan_meter_id'=> $catatan->id,
                'periode_id'      => $catatan->periode_id,
                'total_pemakaian' => $catatan->pemakaian,
                'breakdown_tarif' => $breakdown,
                'biaya_air'       => $biayaAir,
                'biaya_admin'     => $biayaAdmin,
                'biaya_beban'     => $biayaBeban,
                'total_tagihan'   => $totalTagihan,
                'status'          => 'belum_dibayar',
                'tanggal_jatuh_tempo' => now()->addDays(30)->toDateString(),
            ]);
        });
    }

    /**
     * Update angka meter dari sebuah tagihan (via catatan meter terkait),
     * lalu hitung ulang total_pemakaian, breakdown_tarif, biaya_air, dan total_tagihan.
     * biaya_admin & biaya_beban TIDAK diubah — nilai itu tetap mencerminkan
     * konfigurasi yang berlaku saat tagihan pertama kali dibuat.
     */
    public function updateAngkaMeter(
        Tagihan $tagihan,
        float $angkaMeterLalu,
        float $angkaMeterSekarang,
        ?int $diubahOleh = null
    ): Tagihan {
        if ($angkaMeterSekarang < $angkaMeterLalu) {
            throw new \RuntimeException('Angka meter sekarang tidak boleh lebih kecil dari angka meter lalu.');
        }

        return DB::transaction(function () use ($tagihan, $angkaMeterLalu, $angkaMeterSekarang, $diubahOleh) {
            $catatan = $tagihan->catatanMeter;

            if (! $catatan) {
                throw new \RuntimeException('Catatan meter untuk tagihan ini tidak ditemukan.');
            }

            $catatan->update([
                'angka_meter_lalu'     => $angkaMeterLalu,
                'angka_meter_sekarang' => $angkaMeterSekarang,
            ]);

            // Ambil ulang nilai 'pemakaian' terbaru (kolom virtual, dihitung otomatis oleh database)
            $catatan->refresh();

            $tarifs = TarifProgresif::aktif()->get();

            if ($tarifs->isEmpty()) {
                throw new \RuntimeException('Belum ada tarif progresif aktif. Hubungi administrator.');
            }

            [$biayaAir, $breakdown] = TarifProgresif::hitungBiaya($catatan->pemakaian, $tarifs);

            $totalTagihan = $biayaAir + $tagihan->biaya_admin + $tagihan->biaya_beban;

            $tagihan->update([
                'total_pemakaian' => $catatan->pemakaian,
                'breakdown_tarif' => $breakdown,
                'biaya_air'       => $biayaAir,
                'total_tagihan'   => $totalTagihan,
                'diubah_oleh'     => $diubahOleh,
            ]);

            return $tagihan->fresh();
        });
    }

    /**
     * Biaya admin flat — bisa dijadikan config/setting nanti.
     */
    private function getBiayaAdmin(): float
    {
        $config = ConfigGeneral::first();

        if ($config) {
            return (float) $config->admin_fee;
        }

        return 0.00;
    }

    private function getBiayaBeban(): float
    {
        $config = ConfigGeneral::first();

        if ($config) {
            return (float) $config->biaya_beban;
        }

        return 0.00;
    }
}
<?php

namespace App\Services;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PembayaranService
{
    /**
     * Proses pembayaran tunai oleh teller.
     */
    public function bayarTunai(Tagihan $tagihan, int $tellerId): Pembayaran
    {
        if (! $tagihan->isBelumDibayar()) {
            throw new \RuntimeException('Tagihan ini tidak dalam status "Belum Dibayar".');
        }

        return DB::transaction(function () use ($tagihan, $tellerId) {
            $pembayaran = Pembayaran::create([
                'tagihan_id'   => $tagihan->id,
                'metode'       => 'tunai',
                'jumlah_bayar' => $tagihan->total_tagihan,
                'teller_id'    => $tellerId,
                'nomor_nota'   => $this->generateNomorNota(),
                'tanggal_bayar'=> now(),
            ]);

            $tagihan->update(['status' => 'lunas']);

            return $pembayaran;
        });
    }

    /**
     * Pelanggan upload bukti bayar QRIS.
     */
    public function uploadBuktiBayar(Tagihan $tagihan, string $filePath): Pembayaran
    {
        if (! $tagihan->isBelumDibayar()) {
            throw new \RuntimeException('Tagihan ini tidak dalam status "Belum Dibayar".');
        }

        return DB::transaction(function () use ($tagihan, $filePath) {
            $pembayaran = Pembayaran::create([
                'tagihan_id'       => $tagihan->id,
                'metode'           => 'qris',
                'jumlah_bayar'     => $tagihan->total_tagihan,
                'bukti_bayar_path' => $filePath,
                'status_verifikasi'=> 'pending',
                'tanggal_bayar'    => now(),
            ]);

            $tagihan->update(['status' => 'menunggu_verifikasi']);

            return $pembayaran;
        });
    }

    /**
     * Admin verifikasi bukti bayar QRIS.
     */
    public function verifikasiQris(Pembayaran $pembayaran, bool $disetujui, int $adminId, ?string $catatan = null): void
    {
        DB::transaction(function () use ($pembayaran, $disetujui, $adminId, $catatan) {
            $pembayaran->update([
                'status_verifikasi'   => $disetujui ? 'disetujui' : 'ditolak',
                'admin_verifikasi_id' => $adminId,
                'catatan_verifikasi'  => $catatan,
                'tanggal_verifikasi'  => now(),
            ]);

            $pembayaran->tagihan->update([
                'status' => $disetujui ? 'lunas' : 'belum_dibayar',
            ]);
        });
    }

    private function generateNomorNota(): string
    {
        $prefix    = 'NTA-' . date('Ymd');
        $last      = Pembayaran::where('nomor_nota', 'like', $prefix . '%')
                        ->lockForUpdate()
                        ->count();
        $urutan    = str_pad($last + 1, 4, '0', STR_PAD_LEFT);

        return $prefix . '-' . $urutan;
    }
}
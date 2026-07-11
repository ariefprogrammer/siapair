<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function index()
    {
        $header = 1;
        $pelanggan = auth()->user()->pelanggan;

        abort_if(! $pelanggan, 403, 'Akun ini belum terhubung ke data pelanggan.');

        // Tagihan aktif (belum dibayar atau menunggu verifikasi)
        $tagihanAktif = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', ['belum_dibayar', 'menunggu_verifikasi'])
            ->with('periode')
            ->latest()
            ->first();

        // Tagihan 3 bulan terakhir
        $riwayatTagihan = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->with(['periode', 'pembayaran'])
            ->latest()
            ->take(3)
            ->get();

        // Total tunggakan
        $totalTunggakan = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->belumDibayar()
            ->sum('total_tagihan');

        return view('pelanggan.dashboard', compact(
            'pelanggan',
            'tagihanAktif',
            'riwayatTagihan',
            'totalTunggakan',
            'header',
        ));
    }
}
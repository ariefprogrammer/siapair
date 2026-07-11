<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\Pembayaran;
use App\Models\Tagihan;

class DashboardController extends Controller
{
    public function index()
    {
        $header = 1;
        $teller = auth()->user();

        // Statistik hari ini
        $today = today();

        $totalTransaksiHariIni = Pembayaran::where('teller_id', $teller->id)
            ->whereDate('tanggal_bayar', $today)
            ->count();

        $totalPendapatanHariIni = Pembayaran::where('teller_id', $teller->id)
            ->whereDate('tanggal_bayar', $today)
            ->sum('jumlah_bayar');

        $totalBelumDibayar = Tagihan::belumDibayar()->count();

        // Transaksi terbaru (5 terakhir milik teller ini)
        $transaksiTerbaru = Pembayaran::with(['tagihan.pelanggan'])
            ->where('teller_id', $teller->id)
            ->latest('tanggal_bayar')
            ->take(5)
            ->get();

        return view('teller.dashboard', compact(
            'totalTransaksiHariIni',
            'totalPendapatanHariIni',
            'totalBelumDibayar',
            'transaksiTerbaru',
            'header'
        ));
    }
}
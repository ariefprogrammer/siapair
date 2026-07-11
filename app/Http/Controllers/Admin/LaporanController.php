<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CatatanMeter;
use App\Models\Pembayaran;
use App\Models\PeriodePencatatan;
use App\Models\Tagihan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function pdf(Request $request)
    {
        // Hanya admin
        abort_if(auth()->user()->role !== 'admin', 403);

        $periodeId = $request->query('periode_id');
        $periode   = PeriodePencatatan::findOrFail($periodeId);

        $totalPelangganDicatat = CatatanMeter::where('periode_id', $periode->id)->count();
        $totalPemakaian        = CatatanMeter::where('periode_id', $periode->id)
            ->where('status_kondisi', 'normal')
            ->sum('pemakaian');

        $totalLunas      = Tagihan::where('periode_id', $periode->id)->lunas()->count();
        $totalBelumBayar = Tagihan::where('periode_id', $periode->id)->belumDibayar()->count();
        $totalMenunggu   = Tagihan::where('periode_id', $periode->id)->menungguVerifikasi()->count();

        $pendapatanTunai = Pembayaran::tunai()
            ->whereHas('tagihan', fn ($q) => $q->where('periode_id', $periode->id)->where('status', 'lunas'))
            ->sum('jumlah_bayar');

        $pendapatanQris  = Pembayaran::qris()
            ->where('status_verifikasi', 'disetujui')
            ->whereHas('tagihan', fn ($q) => $q->where('periode_id', $periode->id))
            ->sum('jumlah_bayar');

        $totalPendapatan = $pendapatanTunai + $pendapatanQris;

        $detailPelanggan = Tagihan::where('periode_id', $periode->id)
            ->with(['pelanggan', 'pembayaran'])
            ->get();

        $anomali = CatatanMeter::where('periode_id', $periode->id)
            ->where('status_kondisi', '!=', 'normal')
            ->with('pelanggan')
            ->get();

        $pdf = Pdf::loadView('laporan.pdf', compact(
            'periode',
            'totalPelangganDicatat',
            'totalPemakaian',
            'totalLunas',
            'totalBelumBayar',
            'totalMenunggu',
            'pendapatanTunai',
            'pendapatanQris',
            'totalPendapatan',
            'detailPelanggan',
            'anomali',
        ))->setPaper('a4', 'portrait');

        $filename = 'Laporan-SIAPAIR-' . str_replace(' ', '-', $periode->labelBulan()) . '.pdf';

        return $pdf->download($filename);
    }
}
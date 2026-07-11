<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\CatatanMeter;
use App\Models\OperatorPelanggan;
use App\Models\PeriodePencatatan;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $header = 1;
        $operator = auth()->user();

        // Periode aktif
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first();

        // Pelanggan yang ditangani operator ini
        $pelangganIds = OperatorPelanggan::where('operator_id', $operator->id)
            ->pluck('pelanggan_id');

        $totalPelanggan = $pelangganIds->count();

        // Sudah dicatat di periode aktif
        $sudahDicatat = 0;
        $belumDicatat = 0;

        if ($periodeAktif) {
            $sudahDicatat = CatatanMeter::whereIn('pelanggan_id', $pelangganIds)
                ->where('periode_id', $periodeAktif->id)
                ->count();
            $belumDicatat = $totalPelanggan - $sudahDicatat;
        }

        // Catatan terbaru (5 terakhir)
        $catatanTerbaru = CatatanMeter::with('pelanggan')
            ->where('operator_id', $operator->id)
            ->latest('dicatat_at')
            ->take(5)
            ->get();

        return view('operator.dashboard', compact(
            'periodeAktif',
            'totalPelanggan',
            'sudahDicatat',
            'belumDicatat',
            'catatanTerbaru',
            'header'
        ));
    }
}
<?php

namespace App\Filament\Widgets;

use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\PeriodePencatatan;
use App\Models\Tagihan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first();

        // Pendapatan bulan ini (lunas)
        $pendapatanBulanIni = Pembayaran::whereHas('tagihan', function ($q) use ($periodeAktif) {
                if ($periodeAktif) {
                    $q->where('periode_id', $periodeAktif->id);
                }
            })
            ->whereIn('status_verifikasi', ['disetujui', null]) // disetujui (QRIS) atau tunai
            ->whereHas('tagihan', fn ($q) => $q->where('status', 'lunas'))
            ->sum('jumlah_bayar');

        // Pendapatan bulan lalu untuk perbandingan
        $periodeLalu = PeriodePencatatan::where('status', 'tutup')
            ->latest('ditutup_at')
            ->first();

        $pendapatanLalu = $periodeLalu
            ? Pembayaran::whereHas('tagihan', fn ($q) => $q->where('periode_id', $periodeLalu->id)->where('status', 'lunas'))
                ->sum('jumlah_bayar')
            : 0;

        $trendPendapatan = $pendapatanLalu > 0
            ? round((($pendapatanBulanIni - $pendapatanLalu) / $pendapatanLalu) * 100, 1)
            : 0;

        // Total pelanggan aktif
        $totalPelanggan = Pelanggan::aktif()->count();

        // Tagihan belum dibayar
        $totalBelumDibayar = Tagihan::belumDibayar()->count();

        // Menunggu verifikasi QRIS
        $menungguVerifikasi = Pembayaran::pending()->count();

        // Progress pencatatan periode aktif
        $sudahDicatat = 0;
        $progressLabel = 'Tidak ada periode aktif';
        if ($periodeAktif) {
            $sudahDicatat  = \App\Models\CatatanMeter::where('periode_id', $periodeAktif->id)->count();
            $progressLabel = $periodeAktif->labelBulan() . ": {$sudahDicatat}/{$totalPelanggan} dicatat";
        }

        return [
            Stat::make('Total Pelanggan Aktif', number_format($totalPelanggan))
                ->description('Sambungan terdaftar')
                ->descriptionIcon('heroicon-o-users')
                ->color('info'),

            Stat::make('Pendapatan Bulan Ini', 'Rp ' . number_format($pendapatanBulanIni, 0, ',', '.'))
                ->description($trendPendapatan >= 0
                    ? "↑ {$trendPendapatan}% dari bulan lalu"
                    : "↓ " . abs($trendPendapatan) . "% dari bulan lalu")
                ->descriptionIcon($trendPendapatan >= 0 ? 'heroicon-o-arrow-trending-up' : 'heroicon-o-arrow-trending-down')
                ->color($trendPendapatan >= 0 ? 'success' : 'danger'),

            Stat::make('Tagihan Belum Dibayar', number_format($totalBelumDibayar))
                ->description('Perlu segera dilunasi')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color($totalBelumDibayar > 0 ? 'warning' : 'success'),

            Stat::make('Menunggu Verifikasi QRIS', number_format($menungguVerifikasi))
                ->description('Bukti bayar perlu ditinjau')
                ->descriptionIcon('heroicon-o-qr-code')
                ->color($menungguVerifikasi > 0 ? 'warning' : 'success'),

            Stat::make('Progress Pencatatan', $progressLabel)
                ->description($periodeAktif ? 'Periode ' . $periodeAktif->labelBulan() : '-')
                ->descriptionIcon('heroicon-o-clipboard-document-list')
                ->color('primary'),
        ];
    }
}
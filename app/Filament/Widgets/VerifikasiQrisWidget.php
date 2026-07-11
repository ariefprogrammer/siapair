<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PembayaranResource;
use App\Filament\Resources\PelangganResource;
use App\Filament\Resources\PengaduanResource;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\Pelanggan;
use App\Models\Pengaduan;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikasiQrisWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $pending = Pembayaran::qris()->pending()->count();

        $pelangganAktif = Pelanggan::aktif()->count();

        $tagihanBelumLunas = Tagihan::whereIn('status', [
            'belum_dibayar',
            'menunggu_verifikasi',
        ])->count();

        $pengaduanBulanini = Pengaduan::whereMonth('created_at', now()->month)->count();

        $pendapatanQuery = Pembayaran::where(function ($q) {
            $q->where('metode', 'tunai')
                ->orWhere('status_verifikasi', 'disetujui');
        });

        $totalPendapatanBulanIni = (clone $pendapatanQuery)
            ->whereMonth('tanggal_bayar', now()->month)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah_bayar');

        $totalPendapatanBulanLalu = (clone $pendapatanQuery)
            ->whereMonth('tanggal_bayar', now()->subMonth()->month)
            ->whereYear('tanggal_bayar', now()->subMonth()->year)
            ->sum('jumlah_bayar');

        $totalPendapatanTahunIni = (clone $pendapatanQuery)
            ->whereYear('tanggal_bayar', now()->year)
            ->sum('jumlah_bayar');

        return [

            Stat::make('', $pelangganAktif)
                ->description('Pelanggan Aktif')
                ->descriptionIcon('heroicon-o-users')
                ->color('success')
                ->url(PelangganResource::getUrl('index'))
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),

            Stat::make('', $pengaduanBulanini)
                ->description('Pengaduan bulan ini')
                ->descriptionIcon('heroicon-o-exclamation-circle')
                ->color('warning')
                ->url(PengaduanResource::getUrl('index'))
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),

            // Stat::make('Tagihan Belum Lunas', $tagihanBelumLunas)
            //     ->description('Belum dibayar & menunggu verifikasi')
            //     ->descriptionIcon('heroicon-o-clock')
            //     ->color('danger'),

            Stat::make('', $pending)
                ->description($pending > 0 ? 'Verifikasi QRIS' : 'Verifikasi QRIS')
                ->descriptionIcon(
                    $pending > 0
                        ? 'heroicon-o-exclamation-triangle'
                        : 'heroicon-o-check-circle'
                )
                ->color($pending > 0 ? 'warning' : 'success')
                ->url(PembayaranResource::getUrl('index'))
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),

            Stat::make(
                '',
                'Rp ' . number_format($totalPendapatanBulanLalu, 0, ',', '.')
            )
                ->description('Pendapatan Bulan Lalu')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('success')
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),
                
            Stat::make(
                '',
                'Rp ' . number_format($totalPendapatanBulanIni, 0, ',', '.')
            )
                ->description('Pendapatan Bulan Ini')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info')
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),

            Stat::make(
                '',
                'Rp ' . number_format($totalPendapatanTahunIni, 0, ',', '.')
            )
                ->description('Pendapatan Tahun Ini')
                ->descriptionIcon('heroicon-o-chart-bar-square')
                ->color('primary')
                ->extraAttributes([
                    'class' => 'text-center flex flex-col items-center justify-center',
                    'style' => '--col-span: 1; [&>div]:text-center [&>div]:w-full',
                ]),

        ];
    }
}
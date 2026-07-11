<?php

namespace App\Filament\Widgets;

use App\Models\Pembayaran;
use App\Models\PeriodePencatatan;
use Filament\Widgets\ChartWidget;

class PendapatanChartWidget extends ChartWidget
{
    protected static ?string $heading  = 'Pendapatan 6 Bulan Terakhir';
    protected static ?int $sort        = 2;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        // Ambil 6 periode terakhir (tutup atau buka)
        $periodes = PeriodePencatatan::orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $labels   = [];
        $data     = [];

        foreach ($periodes as $periode) {
            $labels[] = $periode->labelBulan();

            $pendapatan = Pembayaran::whereHas('tagihan', fn ($q) =>
                    $q->where('periode_id', $periode->id)->where('status', 'lunas')
                )
                ->sum('jumlah_bayar');

            $data[] = round($pendapatan);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pendapatan (Rp)',
                    'data'            => $data,
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'borderColor'     => 'rgba(59, 130, 246, 1)',
                    'borderWidth'     => 2,
                    'fill'            => true,
                    'tension'         => 0.4,
                    'pointBackgroundColor' => 'rgba(59, 130, 246, 1)',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => ['display' => false],
                'tooltip' => [
                    'callbacks' => [
                        'label' => 'function(ctx) { return "Rp " + ctx.parsed.y.toLocaleString("id-ID"); }',
                    ],
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(v) { return "Rp " + (v/1000).toFixed(0) + "k"; }',
                    ],
                ],
            ],
        ];
    }
}
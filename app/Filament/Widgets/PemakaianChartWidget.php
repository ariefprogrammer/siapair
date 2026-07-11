<?php

namespace App\Filament\Widgets;

use App\Models\CatatanMeter;
use App\Models\PeriodePencatatan;
use Filament\Widgets\ChartWidget;

class PemakaianChartWidget extends ChartWidget
{
    protected static ?string $heading   = 'Total Pemakaian Air 6 Bulan Terakhir';
    protected static ?int $sort         = 3;
    protected static ?string $maxHeight = '280px';

    protected function getData(): array
    {
        $periodes = PeriodePencatatan::orderByDesc('tahun')
            ->orderByDesc('bulan')
            ->take(6)
            ->get()
            ->reverse()
            ->values();

        $labels = [];
        $data   = [];

        foreach ($periodes as $periode) {
            $labels[] = $periode->labelBulan();

            $totalPemakaian = CatatanMeter::where('periode_id', $periode->id)
                ->where('status_kondisi', 'normal')
                ->sum('pemakaian');

            $data[] = round($totalPemakaian, 2);
        }

        return [
            'datasets' => [
                [
                    'label'           => 'Pemakaian (m³)',
                    'data'            => $data,
                    'backgroundColor' => [
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                        'rgba(16, 185, 129, 0.7)',
                    ],
                    'borderColor'  => 'rgba(16, 185, 129, 1)',
                    'borderWidth'  => 1,
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => ['legend' => ['display' => false]],
            'scales'  => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'callback' => 'function(v) { return v + " m³"; }',
                    ],
                ],
            ],
        ];
    }
}
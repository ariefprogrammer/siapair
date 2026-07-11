<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\PemakaianChartWidget;
use App\Filament\Widgets\PendapatanChartWidget;
use App\Filament\Widgets\StatsOverviewWidget;
use App\Filament\Widgets\TagihanMenungguWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon  = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?int $navigationSort     = -1;

    public function getWidgets(): array
    {
        return [
            // StatsOverviewWidget::class,
            PendapatanChartWidget::class,
            PemakaianChartWidget::class,
            // TagihanMenungguWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 2;
    }
}
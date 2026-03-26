<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\DashboardHeaderInfoWidget;
use App\Filament\Widgets\DokumentasiAktivitasTerbaruWidget;
use App\Filament\Widgets\DokumentasiStatsWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Dashboard';

    public function getWidgets(): array
    {
        return [
            DashboardHeaderInfoWidget::class,
            DokumentasiStatsWidget::class,
            DokumentasiAktivitasTerbaruWidget::class,
        ];
    }

    public function getColumns(): int|array
    {
        return [
            'md' => 1,
            'xl' => 1,
        ];
    }
}

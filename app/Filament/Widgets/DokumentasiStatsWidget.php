<?php

namespace App\Filament\Widgets;

use App\Models\Dokumentasi;
use Filament\Widgets\Widget;

class DokumentasiStatsWidget extends Widget
{
    protected static string $view = 'filament.widgets.dokumentasi-stats-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $today = now()->toDateString();
        $query = Dokumentasi::query();

        if ($user?->hasRole('teknisi')) {
            $query->where('user_id', $user->id);
        }

        return [
            'stats' => [
                [
                    'label' => 'Dokumentasi Hari Ini',
                    'value' => (clone $query)->whereDate('created_at', $today)->count(),
                    'subtitle' => 'Input pada ' . now()->format('d M Y'),
                    'icon' => 'heroicon-o-calendar-days',
                    'accent' => 'sky',
                ],
                [
                    'label' => 'Pending',
                    'value' => (clone $query)->where('status', 'pending')->count(),
                    'subtitle' => 'Menunggu verifikasi',
                    'icon' => 'heroicon-o-clock',
                    'accent' => 'amber',
                ],
                [
                    'label' => 'Approved',
                    'value' => (clone $query)->where('status', 'approved')->count(),
                    'subtitle' => 'Siap untuk pelaporan',
                    'icon' => 'heroicon-o-check-circle',
                    'accent' => 'emerald',
                ],
                [
                    'label' => 'Ditolak',
                    'value' => (clone $query)->where('status', 'rejected')->count(),
                    'subtitle' => 'Perlu revisi data',
                    'icon' => 'heroicon-o-x-circle',
                    'accent' => 'rose',
                ],
            ],
        ];
    }
}

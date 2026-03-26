<?php

namespace App\Filament\Widgets;

use App\Models\Dokumentasi;
use Filament\Widgets\Widget;

class DashboardHeaderInfoWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-header-info-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();

        $query = Dokumentasi::query();

        if ($user?->hasRole('teknisi')) {
            $query->where('user_id', $user->id);
        }

        $pending = (clone $query)
            ->where('status', 'pending')
            ->count();

        $approved = (clone $query)
            ->where('status', 'approved')
            ->count();

        return [
            'user' => $user,
            'pendingCount' => $pending,
            'approvedCount' => $approved,
            'canCreateDokumentasi' => $user?->can('create', Dokumentasi::class) ?? false,
            'createDokumentasiUrl' => \App\Filament\Resources\DokumentasiResource::getUrl('create'),
        ];
    }
}

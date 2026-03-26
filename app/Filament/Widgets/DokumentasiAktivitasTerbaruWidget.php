<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\DokumentasiResource;
use App\Models\Dokumentasi;
use Filament\Widgets\Widget;

class DokumentasiAktivitasTerbaruWidget extends Widget
{
    protected static string $view = 'filament.widgets.dokumentasi-aktivitas-terbaru-widget';

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $user = auth()->user();
        $query = Dokumentasi::query()->with(['kategori', 'user'])->latest();

        if ($user?->hasRole('teknisi')) {
            $query->where('user_id', $user->id);
        }

        $items = $query
            ->limit(5)
            ->with('attachments')
            ->get();

        return [
            'items' => $items,
            'lihatSemuaUrl' => DokumentasiResource::getUrl('index'),
        ];
    }
}

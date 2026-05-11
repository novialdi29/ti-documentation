<?php

namespace App\Filament\Resources\DokumentasiResource\Pages;

use App\Filament\Resources\DokumentasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Arr;

class ListDokumentasis extends ListRecords
{
    protected static string $resource = DokumentasiResource::class;

    protected function getHeaderActions(): array
    {
        $query = Arr::only(request()->query(), ['tableFilters', 'tableSearch', 'tableSortColumn', 'tableSortDirection']);

        return [
            Actions\CreateAction::make(),
            Actions\Action::make('exportFilteredPdf')
                ->label('Export PDF (Filter Aktif)')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->url(route('dokumentasi.export-filtered-pdf', $query))
                ->openUrlInNewTab(),
        ];
    }
}

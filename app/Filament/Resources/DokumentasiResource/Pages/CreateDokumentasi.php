<?php

namespace App\Filament\Resources\DokumentasiResource\Pages;

use App\Filament\Resources\DokumentasiResource;
use App\Models\Dokumentasi;
use App\Models\User;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Carbon;

class CreateDokumentasi extends CreateRecord
{
    protected static string $resource = DokumentasiResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = $data['user_id'] ?? auth()->id();
        $data['nomor_ticket'] = Dokumentasi::generateNomorTicket();
        $data['tanggal_ticket'] = $data['tanggal_ticket'] ?? now()->toDateString();

        if (auth()->user()?->hasRole('teknisi')) {
            $data['status'] = 'pending';
            $data['verified_by'] = null;
            $data['verified_at'] = null;
            $data['catatan_verifikasi'] = null;
        } elseif (in_array($data['status'], ['approved', 'rejected'], true)) {
            $data['verified_by'] = auth()->id();
            $data['verified_at'] = $data['verified_at'] ?? Carbon::now();
        } else {
            $data['verified_by'] = null;
            $data['verified_at'] = null;
            $data['catatan_verifikasi'] = null;
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $verifikators = User::role('verifikator')->get();

        if ($verifikators->isEmpty()) {
            return;
        }

        Notification::make()
            ->title('Dokumentasi baru menunggu verifikasi')
            ->body('Judul: ' . $this->record->judul)
            ->sendToDatabase($verifikators);
    }
}

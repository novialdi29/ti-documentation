<?php

namespace App\Http\Controllers;

use App\Models\Dokumentasi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class DokumentasiPdfController extends Controller
{
    public function show(Dokumentasi $dokumentasi)
    {
        abort_unless(auth()->check(), 403);
        abort_unless(auth()->user()->can('view', $dokumentasi), 403);

        $isExportable = in_array($dokumentasi->status, ['approved', 'verified'], true) || $dokumentasi->verified_at !== null;
        abort_unless($isExportable, 403, 'Dokumentasi hanya dapat diexport setelah approved/verified.');

        $dokumentasi->loadMissing(['kategori', 'user', 'attachments']);

        $attachmentImages = $dokumentasi->attachments
            ->map(function ($attachment): ?array {
                $path = (string) $attachment->file_path;
                $fullPath = Storage::disk('public')->path($path);

                if ($path === '' || ! is_file($fullPath)) {
                    return null;
                }

                $mime = mime_content_type($fullPath) ?: '';
                if (! str_starts_with($mime, 'image/')) {
                    return null;
                }

                $contents = file_get_contents($fullPath);
                if ($contents === false) {
                    return null;
                }

                return [
                    'name' => basename($path),
                    'data_uri' => 'data:' . $mime . ';base64,' . base64_encode($contents),
                ];
            })
            ->filter()
            ->values();

        Carbon::setLocale('id');

        $pdf = Pdf::loadView('pdf.dokumentasi-ticket', [
            'record' => $dokumentasi,
            'attachmentImages' => $attachmentImages,
            'exportDate' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-dokumentasi-' . $dokumentasi->nomor_ticket . '.pdf');
    }

    public function exportFiltered(Request $request)
    {
        abort_unless(auth()->check(), 403);

        $filters = (array) $request->input('tableFilters', []);

        $query = Dokumentasi::query()->with(['kategori', 'user']);

        if (auth()->user()?->hasRole('teknisi')) {
            $query->where('user_id', auth()->id());
        }

        $status = data_get($filters, 'status.value');
        if (filled($status)) {
            $query->where('status', $status);
        }

        $lokasiUnit = data_get($filters, 'lokasi_unit.value');
        if (filled($lokasiUnit)) {
            $query->where('lokasi_unit', $lokasiUnit);
        }

        $kategoriId = data_get($filters, 'kategori.value');
        if (filled($kategoriId)) {
            $query->where('kategori_id', $kategoriId);
        }

        $userId = data_get($filters, 'user.value');
        if (filled($userId) && auth()->user()?->hasAnyRole(['admin', 'verifikator'])) {
            $query->where('user_id', $userId);
        }

        $tanggalDari = data_get($filters, 'tanggal.dari');
        $tanggalSampai = data_get($filters, 'tanggal.sampai');

        $query
            ->when(filled($tanggalDari), fn (Builder $builder): Builder => $builder->whereDate('tanggal_ticket', '>=', $tanggalDari))
            ->when(filled($tanggalSampai), fn (Builder $builder): Builder => $builder->whereDate('tanggal_ticket', '<=', $tanggalSampai));

        $records = $query
            ->orderByDesc('tanggal_ticket')
            ->orderByDesc('created_at')
            ->get();

        Carbon::setLocale('id');

        $pdf = Pdf::loadView('pdf.dokumentasi-rekap', [
            'records' => $records,
            'filters' => $filters,
            'exportDate' => now(),
        ])->setPaper('a4', 'landscape');

        return $pdf->stream('rekap-dokumentasi-filtered-' . now()->format('Ymd-His') . '.pdf');
    }
}

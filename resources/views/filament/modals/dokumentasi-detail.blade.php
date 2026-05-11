@php
    use Illuminate\Support\Facades\Storage;

    $statusColor = match ($record->status) {
        'approved' => 'bg-green-100 text-green-700',
        'rejected' => 'bg-red-100 text-red-700',
        default => 'bg-amber-100 text-amber-700',
    };
@endphp

<div class="max-h-[70vh] space-y-6 overflow-y-auto pr-1">
    <div class="rounded-xl border border-gray-200 bg-white p-4">
        <p class="text-sm text-gray-500">Informasi Ticket</p>
        <div class="mt-3 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <p class="text-xs text-gray-500">Nomor Ticket</p>
                <p class="font-medium text-gray-900">{{ $record->nomor_ticket ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Tanggal Ticket</p>
                <p class="font-medium text-gray-900">{{ $record->tanggal_ticket?->translatedFormat('d F Y') ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Unit / Fakultas</p>
                <p class="font-medium text-gray-900">{{ $record->lokasi_unit ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">PIC Unit</p>
                <p class="font-medium text-gray-900">{{ $record->nama_pic ?: '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Teknisi</p>
                <p class="font-medium text-gray-900">{{ $record->user?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Kategori</p>
                <p class="font-medium text-gray-900">{{ $record->kategori?->nama ?? '-' }}</p>
            </div>
            <div>
                <p class="text-xs text-gray-500">Status</p>
                <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColor }}">
                    {{ ucfirst($record->status) }}
                </span>
            </div>
            <div>
                <p class="text-xs text-gray-500">Catatan Verifikasi</p>
                <p class="font-medium text-gray-900">{{ $record->catatan_verifikasi ?: '-' }}</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
        <div>
            <p class="text-sm text-gray-500">Judul Pekerjaan</p>
            <p class="font-medium text-gray-900">{{ $record->judul }}</p>
        </div>
    </div>

    <div class="space-y-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Keluhan / Permasalahan</p>
            <div class="mt-1 text-sm font-medium text-gray-900">
                {!! nl2br(e(strip_tags((string) $record->deskripsi))) !!}
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Tindakan yang Dilakukan</p>
            <div class="mt-1 text-sm font-medium text-gray-900">
                {!! nl2br(e((string) $record->tindakan)) !!}
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4">
            <p class="text-sm text-gray-500">Hasil Pekerjaan</p>
            <div class="mt-1 text-sm font-medium text-gray-900">
                {!! nl2br(e((string) $record->hasil)) !!}
            </div>
        </div>
    </div>

    <div class="space-y-3">
        <p class="text-sm text-gray-500">Lampiran</p>
        @forelse ($record->attachments as $attachment)
            @php
                $path = (string) $attachment->file_path;
                $url = $path !== '' ? Storage::disk('public')->url($path) : null;
                $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
                $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp', 'svg'], true);
            @endphp

            <div class="rounded-xl border border-gray-200 bg-white p-4">
                <p class="mb-3 text-sm font-medium text-gray-900">{{ basename($path) }}</p>

                @if ($url && $isImage)
                    <img
                        src="{{ $url }}"
                        alt="{{ basename($path) }}"
                        class="max-h-72 w-full rounded-lg border border-gray-200 object-contain"
                        loading="lazy"
                    >
                @elseif ($url)
                    <a
                        href="{{ $url }}"
                        target="_blank"
                        class="inline-flex items-center rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-900 transition hover:bg-gray-50"
                        download
                    >
                        Download File
                    </a>
                @else
                    <p class="text-sm text-gray-500">File tidak tersedia.</p>
                @endif
            </div>
        @empty
            <p class="text-sm text-gray-500">Tidak ada lampiran.</p>
        @endforelse
    </div>
</div>


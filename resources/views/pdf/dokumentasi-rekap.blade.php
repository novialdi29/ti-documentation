<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Dokumentasi</title>
    <style>
        @page { margin: 24px 28px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; color: #111; }
        .header { margin-bottom: 12px; border-bottom: 1px solid #222; padding-bottom: 8px; }
        .header img { width: 82%; height: auto; margin: 0 auto; display: block; }
        .title { text-align: center; font-weight: 700; font-size: 13px; text-transform: uppercase; margin: 10px 0; }
        .meta { margin-bottom: 8px; }
        .meta p { margin: 2px 0; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #222; padding: 5px; vertical-align: top; }
        th { background: #f5f5f5; text-transform: uppercase; font-size: 9px; letter-spacing: .04em; }
        .small { color: #444; font-size: 9px; }
    </style>
</head>
<body>
    @php
        $kopPath = public_path('images/kop-upa-tik.png');
        $kopDataUri = null;

        if (is_file($kopPath)) {
            $kopMime = mime_content_type($kopPath) ?: 'image/png';
            $kopContent = file_get_contents($kopPath);
            if ($kopContent !== false) {
                $kopDataUri = 'data:' . $kopMime . ';base64,' . base64_encode($kopContent);
            }
        }
    @endphp

    <div class="header">
        @if ($kopDataUri)
            <img src="{{ $kopDataUri }}" alt="Kop Surat">
        @endif
    </div>

    <div class="title">Rekap Laporan Dokumentasi Pekerjaan Infrastruktur TI</div>

    <div class="meta">
        <p><strong>Tanggal Export:</strong> {{ $exportDate->translatedFormat('d F Y H:i') }}</p>
        <p class="small">Filter aktif diterapkan sesuai tampilan tabel saat export dilakukan.</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Ticket</th>
                <th>Judul</th>
                <th>Unit/Fakultas</th>
                <th>PIC</th>
                <th>Kategori</th>
                <th>Teknisi</th>
                <th>Status</th>
                <th>Tanggal Ticket</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($records as $index => $record)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $record->nomor_ticket }}</td>
                    <td>{{ $record->judul }}</td>
                    <td>{{ $record->lokasi_unit ?: '-' }}</td>
                    <td>{{ $record->nama_pic ?: '-' }}</td>
                    <td>{{ $record->kategori?->nama ?: '-' }}</td>
                    <td>{{ $record->user?->name ?: '-' }}</td>
                    <td>{{ \Illuminate\Support\Str::headline((string) $record->status) }}</td>
                    <td>{{ $record->tanggal_ticket?->translatedFormat('d M Y') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" style="text-align: center;">Tidak ada data dokumentasi sesuai filter.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>

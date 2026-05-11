<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Dokumentasi Pekerjaan</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #111; line-height: 1.4; }
        .text-center { text-align: center; }
        .header { border-bottom: 2px solid #111; padding-bottom: 6px; margin-bottom: 14px; }
        .header img { display: block; width: 94%; height: auto; margin: 0 auto; }
        .title { text-align: center; font-size: 14px; font-weight: 700; margin-bottom: 14px; text-transform: uppercase; }
        table { width: 100%; border-collapse: collapse; }
        .meta td { border: 1px solid #222; padding: 6px 8px; vertical-align: top; }
        .meta td.label { width: 28%; font-weight: 700; background: #f5f5f5; }
        .section-title { font-size: 12px; font-weight: 700; margin: 16px 0 8px; text-transform: uppercase; }
        .content-box { border: 1px solid #222; padding: 8px; margin-bottom: 8px; min-height: 56px; }
        .content-label { font-weight: 700; margin-bottom: 4px; }
        .attachments { margin-top: 8px; }
        .attachment-item { border: 1px solid #222; padding: 8px; margin-bottom: 10px; page-break-inside: avoid; }
        .attachment-item img { width: auto; max-width: 360px; max-height: 220px; height: auto; display: block; margin: 0 auto; object-fit: contain; }
        .footer-sign { width: 100%; margin-top: 28px; }
        .footer-sign td { vertical-align: top; }
        .signature-block { width: 42%; margin-left: auto; text-align: center; }
        .signature-space { height: 64px; }
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

    <div class="header text-center">
        @if ($kopDataUri)
            <img src="{{ $kopDataUri }}" alt="Kop Surat UPA TIK Universitas Riau">
        @else
            <div style="font-weight: 700;">KOP SURAT TIDAK DITEMUKAN (public/images/kop-upa-tik.png)</div>
        @endif
    </div>

    <div class="title">LAPORAN DOKUMENTASI PEKERJAAN</div>

    <table class="meta">
        <tr><td class="label">Nomor Ticket</td><td>{{ $record->nomor_ticket ?: '-' }}</td></tr>
        <tr><td class="label">Tanggal Ticket</td><td>{{ $record->tanggal_ticket?->translatedFormat('d F Y') ?? '-' }}</td></tr>
        <tr><td class="label">Unit / Fakultas</td><td>{{ $record->lokasi_unit ?: '-' }}</td></tr>
        <tr><td class="label">PIC Unit</td><td>{{ $record->nama_pic ?: '-' }}</td></tr>
        <tr><td class="label">Teknisi</td><td>{{ $record->user?->name ?: '-' }}</td></tr>
        <tr><td class="label">Kategori</td><td>{{ $record->kategori?->nama ?: '-' }}</td></tr>
        <tr><td class="label">Status</td><td>{{ ucfirst($record->status) }}</td></tr>
    </table>

    <div class="section-title">Informasi Pekerjaan</div>
    <div class="content-box">
        <div class="content-label">Judul Pekerjaan</div>
        <div>{{ $record->judul ?: '-' }}</div>
    </div>
    <div class="content-box">
        <div class="content-label">Keluhan / Permasalahan</div>
        <div>{!! nl2br(e(strip_tags((string) $record->deskripsi))) !!}</div>
    </div>
    <div class="content-box">
        <div class="content-label">Tindakan</div>
        <div>{!! nl2br(e((string) $record->tindakan)) !!}</div>
    </div>
    <div class="content-box">
        <div class="content-label">Hasil Pekerjaan</div>
        <div>{!! nl2br(e((string) $record->hasil)) !!}</div>
    </div>

    <div class="section-title">Dokumentasi Foto</div>
    <div class="attachments">
        @forelse ($attachmentImages as $image)
            <div class="attachment-item">
                <img src="{{ $image['data_uri'] }}" alt="{{ $image['name'] }}">
            </div>
        @empty
            <div class="content-box">Tidak ada lampiran gambar.</div>
        @endforelse
    </div>

    <table class="footer-sign">
        <tr>
            <td></td>
            <td class="signature-block">
                Pekanbaru, {{ $exportDate->translatedFormat('d F Y') }}<br>
                Staf Infrastruktur TI<br>
                UPA TIK Universitas Riau
                <div class="signature-space"></div>
                (....................................................)
            </td>
        </tr>
    </table>
</body>
</html>

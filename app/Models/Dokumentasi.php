<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Dokumentasi extends Model
{
    protected $fillable = [
        'nomor_ticket',
        'tanggal_ticket',
        'lokasi_unit',
        'nama_pic',
        'kontak_pic',
        'user_id',
        'kategori_id',
        'judul',
        'deskripsi',
        'tindakan',
        'hasil',
        'status',
        'verified_by',
        'verified_at',
        'catatan_verifikasi',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_ticket' => 'date',
            'verified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $dokumentasi): void {
            $dokumentasi->nomor_ticket = $dokumentasi->nomor_ticket ?: self::generateNomorTicket();
            $dokumentasi->tanggal_ticket = $dokumentasi->tanggal_ticket ?: now()->toDateString();
        });
    }

    public static function generateNomorTicket(?int $year = null): string
    {
        $year ??= now()->year;
        $prefix = 'TIK-' . $year . '-';

        $lastNumber = static::query()
            ->where('nomor_ticket', 'like', $prefix . '%')
            ->selectRaw('MAX(CAST(SUBSTRING(nomor_ticket, 10) AS UNSIGNED)) as last_number')
            ->value('last_number');

        $sequence = ((int) $lastNumber) + 1;

        return sprintf('%s%04d', $prefix, $sequence);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(Attachment::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}

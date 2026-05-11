<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumentasiResource\Pages;
use App\Models\Dokumentasi;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DokumentasiResource extends Resource
{
    protected static ?string $model = Dokumentasi::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Dokumentasi';

    protected static ?string $modelLabel = 'Dokumentasi';

    protected static ?string $pluralModelLabel = 'Dokumentasi';

    protected static ?string $navigationGroup = 'Infrastruktur TI';

    public static function form(Form $form): Form
    {
        $canVerify = auth()->user()?->hasAnyRole(['admin', 'verifikator']) ?? false;

        return $form
            ->columns(2)
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->required(),
                Section::make('Informasi Ticket')
                    ->description('Data tiket untuk tracking pekerjaan lapangan.')
                    ->schema([
                        TextInput::make('nomor_ticket')
                            ->label('Nomor Ticket')
                            ->default(fn (): string => Dokumentasi::generateNomorTicket())
                            ->placeholder('Memuat nomor ticket...')
                            ->readOnly()
                            ->dehydrated()
                            ->required(),
                        DatePicker::make('tanggal_ticket')
                            ->label('Tanggal Ticket')
                            ->default(now())
                            ->native(false)
                            ->required(),
                        TextInput::make('lokasi_unit')
                            ->label('Lokasi / Fakultas / Unit')
                            ->placeholder('Contoh: Ruang Server Lt. 2, Fakultas Teknik')
                            ->helperText('Dipakai untuk pelaporan per unit/fakultas.')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('nama_pic')
                            ->label('PIC Unit / Fakultas')
                            ->placeholder('Nama pihak yang menerima / mengetahui pekerjaan')
                            ->required(),
                        TextInput::make('kontak_pic')
                            ->label('Kontak PIC')
                            ->placeholder('No HP atau email (opsional)')
                            ->suffixIcon('heroicon-m-information-circle')
                            ->helperText('Boleh diisi nomor HP atau email PIC.')
                            ->nullable(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-sm p-6']),
                Section::make('Informasi Pekerjaan')
                    ->schema([
                        Select::make('kategori_id')
                            ->label('Kategori')
                            ->relationship(
                                name: 'kategori',
                                titleAttribute: 'nama',
                                modifyQueryUsing: fn (Builder $query) => $query->where('is_active', true),
                            )
                            ->searchable()
                            ->preload()
                            ->placeholder('Pilih kategori pekerjaan')
                            ->native(false)
                            ->required(),
                        TextInput::make('judul')
                            ->label('Judul Ticket')
                            ->placeholder('Contoh: Perbaikan jaringan internet ruang administrasi')
                            ->required()
                            ->maxLength(255),
                        RichEditor::make('deskripsi')
                            ->label('Keluhan / Permasalahan')
                            ->placeholder('Jelaskan keluhan atau permasalahan yang dilaporkan')
                            ->required()
                            ->columnSpanFull(),
                        Textarea::make('tindakan')
                            ->rows(4)
                            ->placeholder('Jelaskan tindakan yang dilakukan')
                            ->columnSpan(1),
                        Textarea::make('hasil')
                            ->rows(4)
                            ->placeholder('Jelaskan hasil akhir pekerjaan')
                            ->columnSpan(1),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-sm p-6']),
                Section::make('Dokumentasi')
                    ->schema([
                        Repeater::make('attachments')
                            ->relationship()
                            ->label('Lampiran Foto / File')
                            ->helperText('Minimal 1 file sebagai bukti pekerjaan.')
                            ->addActionLabel('Tambah Lampiran')
                            ->minItems(1)
                            ->required()
                            ->schema([
                                FileUpload::make('file_path')
                                    ->label('File')
                                    ->disk('public')
                                    ->visibility('public')
                                    ->directory('dokumentasi-attachments')
                                    ->getUploadedFileNameForStorageUsing(
                                        fn (TemporaryUploadedFile $file): string => Str::uuid() . '.' . $file->getClientOriginalExtension(),
                                    )
                                    ->imagePreviewHeight('180')
                                    ->panelLayout('integrated')
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp', 'application/pdf'])
                                    ->maxSize(5120)
                                    ->downloadable()
                                    ->openable()
                                    ->required(),
                            ])
                            ->columns(1)
                            ->columnSpanFull()
                            ->collapsible(),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-sm p-6']),
                Section::make('Validasi')
                    ->schema([
                        Select::make('status')
                            ->options([
                                'pending' => 'Pending',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                            ])
                            ->default('pending')
                            ->disabled(! $canVerify)
                            ->dehydrated()
                            ->helperText('Status hanya dapat diubah admin/verifikator.')
                            ->required(),
                        Textarea::make('catatan_verifikasi')
                            ->label('Catatan Verifikasi')
                            ->rows(3)
                            ->helperText('Hanya admin/verifikator.')
                            ->disabled(! $canVerify)
                            ->visible($canVerify)
                            ->columnSpanFull(),
                    ])
                    ->columns(2)
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'bg-white rounded-2xl border border-gray-200 shadow-sm p-6']),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('judul')
                    ->label('Ticket')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('judul', 'like', "%{$search}%")
                                ->orWhere('nomor_ticket', 'like', "%{$search}%")
                                ->orWhere('deskripsi', 'like', "%{$search}%")
                                ->orWhere('lokasi_unit', 'like', "%{$search}%")
                                ->orWhere('nama_pic', 'like', "%{$search}%")
                                ->orWhereHas('kategori', fn (Builder $kategoriQuery): Builder => $kategoriQuery->where('nama', 'like', "%{$search}%"));
                        });
                    })
                    ->formatStateUsing(fn (?string $state): string => (string) $state)
                    ->description(fn (Dokumentasi $record): string => (string) $record->nomor_ticket)
                    ->weight('medium')
                    ->wrap()
                    ->sortable(),
                TextColumn::make('lokasi_unit')
                    ->label('Unit / Kategori')
                    ->formatStateUsing(function (?string $state, Dokumentasi $record): string {
                        $unit = e((string) $state);
                        $kategori = e((string) ($record->kategori?->nama ?? '-'));

                        return "<div class='text-gray-900'>{$unit}</div><span class='mt-1 inline-flex rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-700'>{$kategori}</span>";
                    })
                    ->html()
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('lokasi_unit', 'like', "%{$search}%")
                                ->orWhereHas('kategori', fn (Builder $kategoriQuery): Builder => $kategoriQuery->where('nama', 'like', "%{$search}%"));
                        });
                    })
                    ->sortable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->toggleable()
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin', 'verifikator']) ?? false),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => Str::headline((string) $state))
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->since()
                    ->tooltip(fn (Dokumentasi $record): ?string => $record->created_at?->translatedFormat('d M Y'))
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->searchable(),
                Filter::make('tanggal')
                    ->label('Periode')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Tanggal Mulai'),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Tanggal Akhir'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_ticket', '>=', $date),
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('tanggal_ticket', '<=', $date),
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['dari'] ?? null) {
                            $indicators['dari'] = 'Dari ' . Carbon::parse($data['dari'])->format('d M Y');
                        }

                        if ($data['sampai'] ?? null) {
                            $indicators['sampai'] = 'Sampai ' . Carbon::parse($data['sampai'])->format('d M Y');
                        }

                        return $indicators;
                    }),
                SelectFilter::make('lokasi_unit')
                    ->label('Unit')
                    ->options(function (): array {
                        return Dokumentasi::query()
                            ->whereNotNull('lokasi_unit')
                            ->where('lokasi_unit', '!=', '')
                            ->distinct()
                            ->orderBy('lokasi_unit')
                            ->pluck('lokasi_unit', 'lokasi_unit')
                            ->toArray();
                    })
                    ->searchable(),
                SelectFilter::make('kategori')
                    ->label('Kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user')
                    ->label('Teknisi')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->visible(fn (): bool => auth()->user()?->hasAnyRole(['admin', 'verifikator']) ?? false),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                ActionGroup::make([
                    Action::make('lihat')
                        ->label('Lihat')
                        ->icon('heroicon-o-eye')
                        ->color('primary')
                        ->authorize(fn (Dokumentasi $record): bool => auth()->user()?->can('view', $record) ?? false)
                        ->modalHeading('Detail Dokumentasi')
                        ->modalWidth('7xl')
                        ->modalSubmitAction(false)
                        ->modalCancelActionLabel('Close')
                        ->modalContent(fn (Dokumentasi $record) => view('filament.modals.dokumentasi-detail', [
                            'record' => $record->loadMissing(['kategori', 'user', 'attachments']),
                        ]))
                        ->extraModalFooterActions(function (Dokumentasi $record): array {
                            $actions = [
                                Action::make('exportPdf')
                                    ->label('Export PDF')
                                    ->icon('heroicon-o-document-arrow-down')
                                    ->color('gray')
                                    ->url(fn (Dokumentasi $record): string => route('dokumentasi.export-pdf', $record))
                                    ->openUrlInNewTab()
                                    ->visible(function (Dokumentasi $record): bool {
                                        $isAllowedStatus = in_array($record->status, ['approved', 'verified'], true) || $record->verified_at !== null;

                                        return $isAllowedStatus && (auth()->user()?->can('view', $record) ?? false);
                                    }),
                            ];

                            if (! (auth()->user()?->can('approve', $record) || auth()->user()?->can('reject', $record))) {
                                return $actions;
                            }

                            return array_merge($actions, [
                                Action::make('approveFromModal')
                                    ->label('Approve')
                                    ->icon('heroicon-o-check-circle')
                                    ->color('success')
                                    ->visible(fn (Dokumentasi $record): bool => auth()->user()?->can('approve', $record) ?? false)
                                    ->action(function (Dokumentasi $record): void {
                                        self::approveDokumentasi($record, null);
                                    }),
                                Action::make('rejectFromModal')
                                    ->label('Reject')
                                    ->icon('heroicon-o-x-circle')
                                    ->color('danger')
                                    ->visible(fn (Dokumentasi $record): bool => auth()->user()?->can('reject', $record) ?? false)
                                    ->form([
                                        Textarea::make('catatan_verifikasi')
                                            ->label('Catatan Verifikasi')
                                            ->rows(3)
                                            ->helperText('Opsional'),
                                    ])
                                    ->action(function (Dokumentasi $record, array $data): void {
                                        self::rejectDokumentasi($record, $data['catatan_verifikasi'] ?? null);
                                    }),
                            ]);
                        }),
                    Action::make('approve')
                        ->label('Approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->authorize(fn (Dokumentasi $record): bool => auth()->user()?->can('approve', $record) ?? false)
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('catatan_verifikasi')
                                ->label('Catatan Verifikasi')
                                ->rows(3)
                                ->helperText('Opsional'),
                        ])
                        ->action(function (Dokumentasi $record, array $data): void {
                            self::approveDokumentasi($record, $data['catatan_verifikasi'] ?? null);
                        })
                        ->visible(fn (Dokumentasi $record): bool => auth()->user()?->can('approve', $record) ?? false),
                    Action::make('reject')
                        ->label('Reject')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->authorize(fn (Dokumentasi $record): bool => auth()->user()?->can('reject', $record) ?? false)
                        ->requiresConfirmation()
                        ->form([
                            Textarea::make('catatan_verifikasi')
                                ->label('Catatan Verifikasi')
                                ->rows(3)
                                ->helperText('Opsional'),
                        ])
                        ->action(function (Dokumentasi $record, array $data): void {
                            self::rejectDokumentasi($record, $data['catatan_verifikasi'] ?? null);
                        })
                        ->visible(fn (Dokumentasi $record): bool => auth()->user()?->can('reject', $record) ?? false),
                    EditAction::make(),
                    Action::make('exportPdf')
                        ->label('Export PDF')
                        ->icon('heroicon-o-document-arrow-down')
                        ->url(fn (Dokumentasi $record): string => route('dokumentasi.export-pdf', $record))
                        ->openUrlInNewTab()
                        ->visible(function (Dokumentasi $record): bool {
                            $isAllowedStatus = in_array($record->status, ['approved', 'verified'], true) || $record->verified_at !== null;

                            return $isAllowedStatus && (auth()->user()?->can('view', $record) ?? false);
                        }),
                ])
                    ->label('Actions')
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->button()
                    ->size('sm')
                    ->color('gray'),
            ])
            ->emptyStateIcon('heroicon-o-document-text')
            ->emptyStateHeading('Belum ada dokumentasi')
            ->emptyStateDescription('Mulai buat ticket dokumentasi pertama')
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->label('Tambah Dokumentasi'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDokumentasis::route('/'),
            'create' => Pages\CreateDokumentasi::route('/create'),
            'edit' => Pages\EditDokumentasi::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        if (auth()->user()?->hasRole('teknisi')) {
            return $query->where('user_id', auth()->id());
        }

        return $query;
    }

    private static function approveDokumentasi(Dokumentasi $record, ?string $catatanVerifikasi): void
    {
        $record->update([
            'status' => 'approved',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $catatanVerifikasi,
        ]);

        if ($record->user) {
            Notification::make()
                ->title('Dokumentasi Anda disetujui')
                ->body('Judul: ' . $record->judul)
                ->sendToDatabase($record->user);
        }

        Notification::make()
            ->title('Dokumentasi disetujui.')
            ->success()
            ->send();
    }

    private static function rejectDokumentasi(Dokumentasi $record, ?string $catatanVerifikasi): void
    {
        $record->update([
            'status' => 'rejected',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'catatan_verifikasi' => $catatanVerifikasi,
        ]);

        if ($record->user) {
            Notification::make()
                ->title('Dokumentasi Anda ditolak')
                ->body('Judul: ' . $record->judul)
                ->sendToDatabase($record->user);
        }

        Notification::make()
            ->title('Dokumentasi ditolak.')
            ->success()
            ->send();
    }
}


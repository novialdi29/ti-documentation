<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DokumentasiResource\Pages;
use App\Models\Dokumentasi;
use Filament\Notifications\Notification;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Filament\Forms\Get;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;
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
        return $form
            ->schema([
                Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->required(),
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
                    ->required()
                    ->maxLength(255)
                    ->columnSpanFull(),
                RichEditor::make('deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('tindakan')
                    ->rows(4)
                    ->columnSpanFull(),
                Textarea::make('hasil')
                    ->rows(4)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ])
                    ->default('pending')
                    ->disabled(fn () => auth()->user()?->hasRole('teknisi'))
                    ->required(),
                DateTimePicker::make('verified_at')
                    ->seconds(false)
                    ->label('Diverifikasi Pada')
                    ->disabled(fn () => auth()->user()?->hasRole('teknisi')),
                Textarea::make('catatan_verifikasi')
                    ->label('Catatan Verifikasi')
                    ->rows(3)
                    ->helperText('Opsional')
                    ->columnSpanFull()
                    ->visible(fn () => auth()->user()?->hasAnyRole(['admin', 'verifikator'])),
                Repeater::make('attachments')
                    ->relationship()
                    ->label('Lampiran')
                    ->addActionLabel('Tambah File')
                    ->schema([
                        FileUpload::make('file_path')
                            ->label('File')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('dokumentasi-attachments')
                            ->getUploadedFileNameForStorageUsing(
                                fn (TemporaryUploadedFile $file): string => Str::uuid() . '.' . $file->getClientOriginalExtension(),
                            )
                            ->imagePreviewHeight('220')
                            ->panelLayout('integrated')
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->previewable(false)
                            ->downloadable()
                            ->openable()
                            ->required(),
                        Placeholder::make('preview_image')
                            ->label('Preview')
                            ->visible(fn (Get $get): bool => filled($get('file_path')))
                            ->content(function (Get $get): HtmlString {
                                $path = self::resolveAttachmentPath($get('file_path'));

                                if (! is_string($path) || $path === '') {
                                    return new HtmlString('');
                                }

                                $url = str_starts_with($path, 'http://') || str_starts_with($path, 'https://')
                                    ? $path
                                    : Storage::disk('public')->url($path);

                                return new HtmlString(
                                    '<img src="' . e($url) . '" alt="Preview lampiran" class="h-40 w-auto rounded-xl border border-gray-200 object-cover" loading="lazy" />',
                                );
                            }),
                    ])
                    ->columns(1)
                    ->columnSpanFull()
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('judul')
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        return $query->where(function (Builder $query) use ($search): void {
                            $query
                                ->where('judul', 'like', "%{$search}%")
                                ->orWhere('deskripsi', 'like', "%{$search}%");
                        });
                    })
                    ->limit(40),
                TextColumn::make('kategori.nama')
                    ->label('Kategori')
                    ->badge()
                    ->color(function (?string $state): string {
                        $palette = ['gray', 'info', 'primary', 'success', 'warning'];
                        $index = abs(crc32((string) $state)) % count($palette);

                        return $palette[$index];
                    })
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'approved',
                        'danger' => 'rejected',
                    ])
                    ->sortable(),
                TextColumn::make('catatan_verifikasi')
                    ->label('Catatan Verifikasi')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('verifier.name')
                    ->label('Diverifikasi Oleh')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
                TextColumn::make('verified_at')
                    ->label('Verified')
                    ->since()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'approved' => 'Approved',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('kategori')
                    ->relationship('kategori', 'nama')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('tanggal')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('dari')
                            ->label('Dari'),
                        \Filament\Forms\Components\DatePicker::make('sampai')
                            ->label('Sampai'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['dari'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['sampai'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
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
            ])
            ->actions([
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
                        if (! (auth()->user()?->can('approve', $record) || auth()->user()?->can('reject', $record))) {
                            return [];
                        }

                        return [
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
                        ];
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
                Tables\Actions\EditAction::make(),
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

    private static function resolveAttachmentPath(mixed $fileState): ?string
    {
        if (is_string($fileState) && $fileState !== '') {
            return $fileState;
        }

        if (! is_array($fileState)) {
            return null;
        }

        foreach ($fileState as $value) {
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        return null;
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


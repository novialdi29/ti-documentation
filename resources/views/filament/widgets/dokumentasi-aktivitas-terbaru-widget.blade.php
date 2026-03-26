<x-filament-widgets::widget>
    <section class="mt-8 space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-semibold text-gray-900">Aktivitas Terbaru</h2>
                <p class="text-sm text-gray-500">5 dokumentasi terakhir untuk monitoring dan tindak lanjut cepat</p>
            </div>
            <a
                href="{{ $lihatSemuaUrl }}"
                class="inline-flex items-center gap-1 rounded-lg border border-gray-200 bg-white px-3 py-1.5 text-sm font-semibold text-gray-700 transition-all duration-200 hover:scale-[1.01] hover:bg-gray-50"
            >
                <x-heroicon-o-list-bullet class="h-4 w-4" />
                Lihat semua
            </a>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full table-fixed divide-y divide-gray-200">
                    <colgroup>
                        <col style="width: 30%;">
                        <col style="width: 14%;">
                        <col style="width: 14%;">
                        <col style="width: 12%;">
                        <col style="width: 18%;">
                        <col style="width: 12%;">
                    </colgroup>
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Judul</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Kategori</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Teknisi</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse ($items as $item)
                            @php
                                $statusClass = match ($item->status) {
                                    'approved' => 'bg-green-100 text-green-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                    default => 'bg-amber-100 text-amber-700',
                                };
                                $modalId = 'aktivitas-dokumentasi-' . $item->id;
                            @endphp
                            <tr class="transition-all duration-200 hover:bg-gray-50">
                                <td class="truncate px-4 py-3 text-sm font-medium text-gray-900">{{ $item->judul }}</td>
                                <td class="truncate px-4 py-3 text-sm text-gray-700">{{ $item->kategori?->nama ?? '-' }}</td>
                                <td class="truncate px-4 py-3 text-sm text-gray-700">{{ $item->user?->name ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="truncate px-4 py-3 text-sm text-gray-700">{{ $item->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                <td class="px-4 py-3 text-left">
                                    <button
                                        type="button"
                                        x-on:click="$dispatch('open-modal', { id: '{{ $modalId }}' })"
                                        class="inline-flex items-center gap-1 rounded-md border border-gray-300 bg-transparent px-2.5 py-1.5 text-xs font-semibold text-gray-700 transition-all duration-200 hover:scale-[1.01] hover:bg-gray-100"
                                    >
                                        <x-heroicon-o-eye class="h-3.5 w-3.5" />
                                        Buka
                                    </button>

                                    <x-filament::modal :id="$modalId" width="7xl" sticky-header>
                                        <x-slot name="heading">
                                            Detail Dokumentasi
                                        </x-slot>

                                        @include('filament.modals.dokumentasi-detail', ['record' => $item])
                                    </x-filament::modal>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center">
                                    <div class="flex flex-col items-center justify-center gap-2 text-gray-500">
                                        <x-heroicon-o-document-text class="h-8 w-8" />
                                        <p class="text-sm font-medium text-gray-700">Belum ada dokumentasi terbaru</p>
                                        <p class="text-xs text-gray-500">Mulai input dokumentasi untuk menampilkan aktivitas tim.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</x-filament-widgets::widget>


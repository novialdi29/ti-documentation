<x-filament-widgets::widget>
    <section
        class="ti-dashboard-header relative overflow-hidden rounded-xl p-6 shadow-sm transition-all duration-200"
    >
        <div class="pointer-events-none absolute -top-10 right-0 h-28 w-28 rounded-full bg-blue-200/40 blur-2xl"></div>

        <div class="relative flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-[320px] max-w-[760px] space-y-2">
                <h1 class="text-xl font-semibold tracking-tight sm:text-2xl">
                    Selamat datang, {{ $user?->name ?? 'User' }}
                </h1>
                <p class="text-sm text-white/90">
                    Ringkasan aktivitas dokumentasi pekerjaan hari ini
                </p>

                <div class="flex flex-wrap items-center gap-2 pt-1">
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                        <x-heroicon-o-clock class="h-3.5 w-3.5" />
                        Pending: {{ $pendingCount }}
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full border border-white/25 bg-white/15 px-3 py-1 text-xs font-medium text-white backdrop-blur">
                        <x-heroicon-o-check-circle class="h-3.5 w-3.5" />
                        Approved: {{ $approvedCount }}
                    </span>
                </div>
            </div>

            <div class="ml-auto flex flex-wrap items-center justify-end gap-3">
                <span class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/15 px-3 py-1.5 text-xs font-medium text-white backdrop-blur">
                    <x-heroicon-o-user-circle class="h-4 w-4" />
                    {{ $user?->email ?? 'no-email' }}
                </span>

                @if ($canCreateDokumentasi)
                    <a
                        href="{{ $createDokumentasiUrl }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-white/30 bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:scale-[1.01] hover:bg-gray-800"
                    >
                        <x-heroicon-o-plus class="h-4 w-4" />
                        Tambah Dokumentasi
                    </a>
                @endif
            </div>
        </div>
    </section>
</x-filament-widgets::widget>


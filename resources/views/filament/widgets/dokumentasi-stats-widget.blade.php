<x-filament-widgets::widget>
    <section wire:poll.10s class="mt-8 space-y-4">
        <div>
            <h2 class="text-lg font-semibold text-gray-900">Statistik Utama</h2>
            <p class="text-sm text-gray-500">Data penting untuk monitoring progres dan kebutuhan pelaporan SKP</p>
        </div>

        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
            @foreach ($stats as $stat)
                @php
                    $accentClasses = match ($stat['accent']) {
                        'amber' => 'bg-amber-50 text-amber-600 ring-amber-200/60',
                        'emerald' => 'bg-emerald-50 text-emerald-600 ring-emerald-200/60',
                        'rose' => 'bg-rose-50 text-rose-600 ring-rose-200/60',
                        default => 'bg-sky-50 text-sky-600 ring-sky-200/60',
                    };
                @endphp

                <article class="rounded-2xl border border-gray-200 bg-white/95 p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:scale-[1.01] hover:shadow-lg">
                    <div class="flex items-start justify-between">
                        <div class="inline-flex h-10 w-10 items-center justify-center rounded-full ring-1 backdrop-blur-sm {{ $accentClasses }}">
                            <x-filament::icon :icon="$stat['icon']" class="h-5 w-5" />
                        </div>
                    </div>

                    <p class="mt-4 text-sm font-medium text-gray-500">{{ $stat['label'] }}</p>
                    <p class="mt-1 text-3xl font-bold tracking-tight text-gray-900">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-sm text-gray-400">{{ $stat['subtitle'] }}</p>
                </article>
            @endforeach
        </div>
    </section>
</x-filament-widgets::widget>


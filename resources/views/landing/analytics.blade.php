{{-- Analytics preview. Clearly labelled sample data: it shows the product, it does not claim results. --}}
@php
    $series = collect(range(29, 0))->mapWithKeys(fn (int $ago) => [
        now()->subDays($ago)->toDateString() => (int) round(34 + 14 * sin((29 - $ago) / 3.2) + (29 - $ago) * 0.9 + (($ago * 7) % 5)),
    ])->all();
    $devices = [__('Mobile') => 82, __('Tablet') => 7, __('Desktop') => 11];
    $countries = ['EG' => 61, 'SA' => 24, 'AE' => 9];
    $points = [
        ['icon' => 'heroicon-o-chart-bar', 'text' => __('Scans per day for the last 30 days')],
        ['icon' => 'heroicon-o-users', 'text' => __('Unique visitors, counted without storing IP addresses')],
        ['icon' => 'heroicon-o-device-phone-mobile', 'text' => __('Devices and countries at a glance')],
    ];
@endphp
<section id="analytics" class="border-y border-line bg-surface">
    <div class="mx-auto grid max-w-6xl items-center gap-12 px-4 py-20 sm:px-6 lg:grid-cols-[1fr_1.25fr] lg:py-28">
        <div>
            <x-landing.heading :eyebrow="__('Analytics')" :title="__('See what your codes are doing')" :description="__('Every scan is counted the moment it happens, so you know which poster, table or flyer actually works.')" />
            <ul class="mt-8 space-y-4">
                @foreach ($points as $point)
                    <li class="flex items-start gap-3 text-ink">
                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-primary-soft text-primary-ink">
                            <x-dynamic-component :component="$point['icon']" class="size-5" aria-hidden="true" />
                        </span>
                        <span class="pt-1.5">{{ $point['text'] }}</span>
                    </li>
                @endforeach
            </ul>
            <p class="mt-6 text-sm text-ink-subtle">{{ __('Scan analytics are included in every paid plan.') }}</p>
        </div>

        <figure class="relative">
            <div class="absolute -inset-4 rounded-[2rem] bg-brand-gradient opacity-15 blur-2xl" aria-hidden="true"></div>
            <div class="relative rounded-2xl border border-line bg-page p-5 shadow-raised sm:p-6">
                <div class="flex items-center justify-between gap-3">
                    <p class="font-semibold text-ink">{{ __('Table menu') }}</p>
                    <x-ui.badge color="warning">{{ __('Example data') }}</x-ui.badge>
                </div>
                <div class="mt-5 grid grid-cols-2 gap-3">
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <p class="text-xs text-ink-subtle">{{ __('Scans, last 30 days') }}</p>
                        <p class="mt-1 text-2xl font-bold text-ink tabular-nums">{{ Number::format(array_sum($series)) }}</p>
                    </div>
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <p class="text-xs text-ink-subtle">{{ __('Unique visitors') }}</p>
                        <p class="mt-1 text-2xl font-bold text-ink tabular-nums">{{ Number::format((int) round(array_sum($series) * 0.71)) }}</p>
                    </div>
                </div>
                <div class="mt-3 rounded-xl border border-line bg-surface p-4">
                    <x-ui.bar-chart :series="$series" :label="__('Scans per day, last 30 days')" />
                </div>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <p class="text-xs font-medium text-ink-subtle">{{ __('Devices') }}</p>
                        <ul class="mt-3 space-y-2.5">
                            @foreach ($devices as $device => $share)
                                <li class="text-sm">
                                    <div class="flex justify-between text-ink"><span>{{ $device }}</span><span class="tabular-nums">{{ Number::percentage($share) }}</span></div>
                                    <div class="mt-1 h-1.5 rounded-full bg-sunken"><div class="h-full rounded-full bg-brand-gradient" style="width: {{ $share }}%"></div></div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="rounded-xl border border-line bg-surface p-4">
                        <p class="text-xs font-medium text-ink-subtle">{{ __('Top countries') }}</p>
                        <ul class="mt-3 space-y-2.5 text-sm text-ink">
                            @foreach ($countries as $country => $share)
                                <li class="flex justify-between gap-2"><span>{{ \Locale::getDisplayRegion('-'.$country, app()->getLocale()) }}</span><span class="text-ink-subtle tabular-nums">{{ Number::percentage($share) }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <figcaption class="mt-3 text-center text-xs text-ink-subtle">{{ __('Illustration with sample data, not real results.') }}</figcaption>
        </figure>
    </div>
</section>

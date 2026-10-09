{{-- Scan analytics on the QR detail page: full breakdown on paid plans, an upgrade card on free. --}}
@if ($plan->is_free)
    <x-ui.card :heading="__('Scan analytics')">
        <div class="flex flex-col items-start gap-4 sm:flex-row sm:items-center">
            <span class="grid size-12 shrink-0 place-items-center rounded-full bg-primary-soft text-primary-ink"><x-heroicon-o-chart-bar class="size-6" aria-hidden="true" /></span>
            <div class="min-w-0 flex-1">
                <p class="font-medium text-ink">{{ __('See when, where and on which devices this code is scanned.') }}</p>
                <p class="mt-1 text-sm text-ink-muted">{{ __('Daily scans, unique visitors, devices and countries are included in every paid plan.') }}</p>
            </div>
            <x-ui.button :href="route('billing')">{{ __('Upgrade your plan') }}</x-ui.button>
        </div>
    </x-ui.card>
@else
    @php($stats = app(\App\Services\ScanStats::class)->forQrCode($qrCode))
    <x-ui.card :heading="__('Scan analytics')" :description="__('Last 30 days')">
        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <p class="text-sm text-ink-muted">{{ __('Scans') }}</p>
                <p class="text-2xl font-semibold text-ink tabular-nums">{{ Number::format($stats['total']) }}</p>
            </div>
            <div>
                <p class="text-sm text-ink-muted">{{ __('Unique visitors') }}</p>
                <p class="text-2xl font-semibold text-ink tabular-nums">{{ Number::format($stats['unique']) }}</p>
            </div>
        </div>
        <x-ui.bar-chart :series="$stats['daily']" :label="__('Scans per day, last 30 days')" class="mt-6" />

        <div class="mt-8 grid gap-8 sm:grid-cols-2">
            @foreach ([__('Devices') => collect($stats['devices'])->mapWithKeys(fn ($n, $device) => [__(ucfirst($device)) => $n])->all(), __('Top countries') => $stats['countries']] as $heading => $rows)
                <div>
                    <h3 class="text-sm font-semibold text-ink">{{ $heading }}</h3>
                    @if (empty($rows))
                        <p class="mt-3 text-sm text-ink-subtle">{{ __('No data yet.') }}</p>
                    @else
                        <ul class="mt-3 space-y-2">
                            @foreach ($rows as $name => $total)
                                <li>
                                    <div class="flex justify-between text-sm"><span class="text-ink">{{ $name }}</span><span class="text-ink-muted tabular-nums">{{ Number::format($total) }}</span></div>
                                    <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-sunken"><div class="h-full rounded-full bg-primary" style="width: {{ $stats['total'] > 0 ? round($total / $stats['total'] * 100) : 0 }}%"></div></div>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    </x-ui.card>
@endif

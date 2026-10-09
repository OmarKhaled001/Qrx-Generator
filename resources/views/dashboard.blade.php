<x-layouts::app :title="__('Overview')">
    <x-ui.page-header :heading="__('Hi :name', ['name' => auth()->user()->name])" :description="__('Here is how your QR codes are doing.')">
        @if (Route::has('qr.create'))
            <x-slot:actions>
                <x-ui.button :href="route('qr.create')" icon="heroicon-o-plus">{{ __('Create QR code') }}</x-ui.button>
            </x-slot:actions>
        @endif
    </x-ui.page-header>

    <div class="grid gap-4 sm:grid-cols-3">
        <x-ui.stat :label="__('QR codes')" :value="Number::format($qrCount)" :hint="__(':used of :limit used', ['used' => Number::format($qrCount), 'limit' => Number::format($plan->qr_limit)])" icon="heroicon-o-qr-code">
            <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-sunken" role="progressbar" aria-valuemin="0" aria-valuemax="{{ $plan->qr_limit }}" aria-valuenow="{{ $qrCount }}" aria-label="{{ __('QR codes') }}">
                <div class="h-full rounded-full bg-primary" style="width: {{ $plan->qr_limit > 0 ? min(100, round($qrCount / $plan->qr_limit * 100)) : 0 }}%"></div>
            </div>
        </x-ui.stat>
        <x-ui.stat :label="__('Total scans')" :value="Number::format($totalScans)" icon="heroicon-o-cursor-arrow-rays" />
        <x-ui.stat :label="__('Current plan')" :value="__($plan->name)" :hint="__(':scans scans per QR code', ['scans' => Number::format($plan->scan_limit)])" icon="heroicon-o-credit-card" />
    </div>

    @if ($qrCount > 0)
        <x-ui.card :heading="__('Scans')" :description="__('Last 30 days')" class="mt-8">
            <x-ui.bar-chart :series="$dailyScans" :label="__('Scans per day, last 30 days')" />
        </x-ui.card>
    @endif

    <div class="mt-8">
        @if ($topQrCodes->isEmpty())
            <x-ui.empty-state :heading="__('No QR codes yet')" :description="__('Create your first dynamic QR code. You can change where it points at any time.')">
                @if (Route::has('qr.create'))
                    <x-ui.button :href="route('qr.create')" icon="heroicon-o-plus">{{ __('Create QR code') }}</x-ui.button>
                @endif
            </x-ui.empty-state>
        @else
            <x-ui.card :heading="__('Most scanned')" :padded="false">
                <ul class="divide-y divide-line">
                    @foreach ($topQrCodes as $qrCode)
                        <li class="flex items-center gap-4 px-5 py-3">
                            <span class="grid size-9 shrink-0 place-items-center rounded-md bg-primary-soft text-primary-ink">
                                <x-dynamic-component :component="$qrCode->type->icon()" class="size-5" aria-hidden="true" />
                            </span>
                            <div class="min-w-0 flex-1">
                                @if (Route::has('qr.show'))
                                    <a href="{{ route('qr.show', $qrCode) }}" class="block truncate text-sm font-medium text-ink hover:underline">{{ $qrCode->name }}</a>
                                @else
                                    <p class="truncate text-sm font-medium text-ink">{{ $qrCode->name }}</p>
                                @endif
                                <p class="truncate text-xs text-ink-subtle">{{ $qrCode->type->label() }}</p>
                            </div>
                            <p class="text-sm font-semibold text-ink tabular-nums">{{ trans_choice(':count scan|:count scans', $qrCode->scan_count, ['count' => Number::format($qrCode->scan_count)]) }}</p>
                        </li>
                    @endforeach
                </ul>
            </x-ui.card>
        @endif
    </div>
</x-layouts::app>

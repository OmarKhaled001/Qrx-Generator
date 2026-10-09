<div>
    <x-ui.page-header :heading="$qrCode->name">
        <x-slot:actions>
            <x-ui.button :href="route('qr.edit', $qrCode)" variant="secondary" icon="heroicon-o-pencil-square" wire:navigate>{{ __('Edit') }}</x-ui.button>
            <x-ui.button variant="secondary" :icon="$qrCode->isPaused() ? 'heroicon-o-play' : 'heroicon-o-pause'" wire:click="toggleStatus">
                {{ $qrCode->isPaused() ? __('Resume') : __('Pause') }}
            </x-ui.button>
            <x-ui.button variant="danger" icon="heroicon-o-trash" wire:click="delete" wire:confirm="{{ __('Delete this QR code? Printed copies will stop working. This cannot be undone.') }}">{{ __('Delete') }}</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($limitReached)
        <x-ui.alert type="danger" :heading="__('This code has reached its scan limit.')" class="mb-6">
            {{ __('People who scan it see a notice instead of your content.') }}
            @if (Route::has('billing'))
                <a href="{{ route('billing') }}" class="font-semibold underline">{{ __('Upgrade your plan') }}</a>
            @endif
        </x-ui.alert>
    @elseif ($qrCode->isPaused())
        <x-ui.alert type="warning" class="mb-6">{{ __('This code is paused. People who scan it see a "temporarily unavailable" page.') }}</x-ui.alert>
    @endif

    <div class="grid gap-6 lg:grid-cols-[22rem_minmax(0,1fr)]">
        <x-ui.card>
            <div class="qr-stage grid place-items-center rounded-md p-6">
                <img src="{{ $preview }}" alt="{{ __('QR code for :name', ['name' => $qrCode->name]) }}" class="aspect-square w-full max-w-64 rounded-sm shadow-card">
            </div>

            <form method="GET" action="{{ route('qr.download', $qrCode) }}" class="mt-5 space-y-4">
                <div class="grid grid-cols-2 gap-3">
                    <x-ui.field :label="__('Format')" for="download-format">
                        <x-ui.select id="download-format" name="format">
                            <option value="png">PNG</option>
                            <option value="svg">SVG</option>
                            @if ($canDownloadEps)
                                <option value="eps">EPS</option>
                            @endif
                        </x-ui.select>
                    </x-ui.field>
                    <x-ui.field :label="__('Size')" for="download-size">
                        <x-ui.select id="download-size" name="size">
                            @foreach ([500, 1000, 2000] as $size)
                                <option value="{{ $size }}" @selected($size === 1000)>{{ __(':size px', ['size' => Number::format($size)]) }}</option>
                            @endforeach
                        </x-ui.select>
                    </x-ui.field>
                </div>
                <x-ui.button type="submit" icon="heroicon-o-arrow-down-tray" class="w-full">{{ __('Download') }}</x-ui.button>
                @unless ($canDownloadEps)
                    <p class="text-xs text-ink-subtle">{{ __('EPS for professional print is available on paid plans.') }}</p>
                @endunless
            </form>
        </x-ui.card>

        <div class="space-y-6">
            <x-ui.card :heading="__('Short link')" :description="__('Every print of this code opens this link, so you can change the content without reprinting.')">
                <div class="flex flex-wrap items-center gap-2">
                    <code class="min-w-0 flex-1 truncate rounded-md bg-sunken px-3 py-2 font-mono text-sm text-ink" dir="ltr">{{ $qrCode->shortUrl() }}</code>
                    <x-ui.copy-button :text="$qrCode->shortUrl()" />
                    <x-ui.button :href="$qrCode->shortUrl()" variant="secondary" size="sm" icon="heroicon-o-arrow-top-right-on-square" target="_blank" rel="noopener">{{ __('Open') }}</x-ui.button>
                </div>
            </x-ui.card>

            <div class="grid gap-4 sm:grid-cols-3">
                <x-ui.stat :label="__('Status')" :value="''">
                    <x-qr.status-badge :qr-code="$qrCode" :plan="$plan" class="mt-1" />
                </x-ui.stat>
                <x-ui.stat :label="__('Total scans')" :value="Number::format($qrCode->scan_count)" :hint="__('Limit: :limit', ['limit' => Number::format($plan->scan_limit)])" />
                <x-ui.stat :label="__('Last scan')" :value="$qrCode->last_scanned_at?->diffForHumans() ?? __('Never')" />
            </div>

            <x-ui.card :heading="__('Content')">
                <dl class="grid gap-x-6 gap-y-3 text-sm sm:grid-cols-[10rem_1fr]">
                    <dt class="text-ink-muted">{{ __('Type') }}</dt>
                    <dd class="flex items-center gap-2 text-ink">
                        <x-dynamic-component :component="$qrCode->type->icon()" class="size-4 text-ink-muted" aria-hidden="true" />
                        {{ $qrCode->type->label() }}
                    </dd>
                    @foreach ($qrCode->type->attributes('') as $field => $label)
                        @php($value = $qrCode->payload[$field] ?? null)
                        @continue(blank($value) || $field === 'password')
                        <dt class="text-ink-muted">{{ $label }}</dt>
                        <dd class="break-words text-ink" dir="auto">{{ is_bool($value) ? ($value ? __('Yes') : __('No')) : $value }}</dd>
                    @endforeach
                </dl>
            </x-ui.card>

            @includeWhen(View::exists('livewire.dashboard.partials.qr-analytics'), 'livewire.dashboard.partials.qr-analytics', ['qrCode' => $qrCode, 'plan' => $plan])
        </div>
    </div>
</div>

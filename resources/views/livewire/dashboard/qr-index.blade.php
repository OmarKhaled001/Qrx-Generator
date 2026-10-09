<div>
    <x-ui.page-header :heading="__('QR codes')" :description="__(':used of :limit used', ['used' => Number::format($counts['all']), 'limit' => Number::format($plan->qr_limit)])">
        <x-slot:actions>
            @if ($canCreate)
                <x-ui.button :href="route('qr.create')" icon="heroicon-o-plus" wire:navigate>{{ __('Create QR code') }}</x-ui.button>
            @elseif (Route::has('billing'))
                <x-ui.button :href="route('billing')" icon="heroicon-o-arrow-up-circle">{{ __('Upgrade for more') }}</x-ui.button>
            @endif
        </x-slot:actions>
    </x-ui.page-header>

    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap gap-1 rounded-lg border border-line bg-surface p-1" role="tablist" aria-label="{{ __('Filter by status') }}">
            @foreach (['all' => __('All'), 'active' => __('Active'), 'paused' => __('Paused'), 'limit' => __('Limit reached')] as $filter => $label)
                <button
                    type="button"
                    role="tab"
                    aria-selected="{{ $status === $filter ? 'true' : 'false' }}"
                    wire:click="$set('status', '{{ $filter }}')"
                    @class([
                        'inline-flex items-center gap-2 rounded-md px-3 py-1.5 text-sm font-medium transition',
                        'bg-primary text-on-primary shadow-xs' => $status === $filter,
                        'text-ink-muted hover:bg-sunken hover:text-ink' => $status !== $filter,
                    ])
                >
                    {{ $label }}
                    <span class="tabular-nums opacity-80">{{ Number::format($counts[$filter]) }}</span>
                </button>
            @endforeach
        </div>

        <div class="relative w-full sm:w-72">
            <x-heroicon-o-magnifying-glass class="pointer-events-none absolute start-3 top-1/2 size-4 -translate-y-1/2 text-ink-subtle" aria-hidden="true" />
            <x-ui.input type="search" wire:model.live.debounce.300ms="search" :placeholder="__('Search by name or code')" :aria-label="__('Search QR codes')" class="ps-9" />
        </div>
    </div>

    @if ($qrCodes->isEmpty())
        @if ($counts['all'] === 0)
            <x-ui.empty-state :heading="__('No QR codes yet')" :description="__('Create your first dynamic QR code. You can change where it points at any time.')">
                <x-ui.button :href="route('qr.create')" icon="heroicon-o-plus" wire:navigate>{{ __('Create QR code') }}</x-ui.button>
            </x-ui.empty-state>
        @else
            <x-ui.empty-state icon="heroicon-o-magnifying-glass" :heading="__('Nothing matches')" :description="__('Try another search or filter.')" />
        @endif
    @else
        <ul class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3" wire:loading.class="opacity-60">
            @foreach ($qrCodes as $qrCode)
                <li wire:key="qr-{{ $qrCode->id }}" class="group flex flex-col rounded-lg border border-line bg-surface shadow-card inset-shadow-highlight transition-[border-color,box-shadow,transform] duration-200 hover:-translate-y-0.5 hover:border-line-strong hover:shadow-raised">
                    <a href="{{ route('qr.show', $qrCode) }}" wire:navigate class="flex items-center gap-4 p-4">
                        <span class="qr-stage grid size-24 shrink-0 place-items-center rounded-lg p-2 transition-transform duration-200 group-hover:scale-[1.03]">
                            <img src="{{ $previews[$qrCode->id] }}" alt="{{ __('QR code for :name', ['name' => $qrCode->name]) }}" class="size-20 rounded-sm" loading="lazy">
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate font-medium text-ink group-hover:text-primary-ink">{{ $qrCode->name }}</span>
                            <span class="mt-0.5 flex items-center gap-1.5 text-xs text-ink-subtle">
                                <x-dynamic-component :component="$qrCode->type->icon()" class="size-3.5" aria-hidden="true" />
                                {{ $qrCode->type->label() }}
                            </span>
                            <span class="mt-1 block truncate text-xs text-ink-muted" dir="auto">{{ $qrCode->type->summary($qrCode->payload) }}</span>
                            <span class="mt-2 flex flex-wrap items-center gap-2">
                                <x-qr.status-badge :qr-code="$qrCode" :plan="$plan" />
                                <span class="text-xs text-ink-subtle tabular-nums">{{ trans_choice(':count scan|:count scans', $qrCode->scan_count, ['count' => Number::format($qrCode->scan_count)]) }}</span>
                            </span>
                        </span>
                    </a>
                    <div class="mt-auto flex items-center justify-end gap-1 border-t border-line px-3 py-2">
                        <x-ui.button :href="route('qr.edit', $qrCode)" variant="ghost" size="sm" icon="heroicon-o-pencil-square" wire:navigate>{{ __('Edit') }}</x-ui.button>
                        <x-ui.button variant="ghost" size="sm" :icon="$qrCode->isPaused() ? 'heroicon-o-play' : 'heroicon-o-pause'" wire:click="toggleStatus({{ $qrCode->id }})">
                            {{ $qrCode->isPaused() ? __('Resume') : __('Pause') }}
                        </x-ui.button>
                        <x-ui.button variant="ghost" size="sm" icon="heroicon-o-trash" class="text-danger-600 hover:text-danger-700" wire:click="delete({{ $qrCode->id }})" wire:confirm="{{ __('Delete this QR code? Printed copies will stop working. This cannot be undone.') }}">
                            {{ __('Delete') }}
                        </x-ui.button>
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-6">{{ $qrCodes->links() }}</div>
    @endif
</div>

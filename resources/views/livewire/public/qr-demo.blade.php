<div class="grid items-center gap-8 rounded-2xl border border-line bg-surface p-6 shadow-raised inset-shadow-highlight sm:p-8 lg:grid-cols-[1fr_auto] lg:gap-12">
    <div class="space-y-5">
        <fieldset>
            <legend class="text-sm font-medium text-ink">{{ __('Start from a style') }}</legend>
            <div class="mt-2 flex flex-wrap gap-2">
                @foreach ($presets as $key => $preset)
                    @php($selected = $style === $preset['style'] && $eye === $preset['eye'] && strcasecmp($bg, $preset['bg']) === 0 && ($preset['gradient'] ? $gradient === $preset['gradient'] : $gradient === null && strcasecmp($color, $preset['color']) === 0))
                    <button type="button" wire:click="applyPreset('{{ $key }}')" aria-pressed="{{ $selected ? 'true' : 'false' }}" @class([
                        'flex items-center gap-2 rounded-full border py-1.5 ps-1.5 pe-3 text-sm font-medium transition-colors',
                        'border-primary bg-primary-soft text-primary-ink' => $selected,
                        'border-line text-ink hover:border-line-strong hover:bg-sunken' => ! $selected,
                    ])>
                        <span class="grid size-6 place-items-center rounded-full ring-1 ring-line" style="background: {{ $preset['bg'] }}" aria-hidden="true">
                            <span class="size-3.5 rounded-full" style="background: {{ $preset['gradient'] ? 'linear-gradient(135deg, '.$preset['gradient']['from'].', '.$preset['gradient']['to'].')' : $preset['color'] }}"></span>
                        </span>
                        {{ $preset['label'] }}
                    </button>
                @endforeach
            </div>
        </fieldset>
        <x-ui.field :label="__('Your text or link')" for="demo-text" :hint="__('Nothing is saved. Create a free account to edit it later.')">
            <x-ui.input id="demo-text" wire:model.live.debounce.400ms="text" :placeholder="__('https://example.com/menu')" maxlength="500" dir="auto" />
        </x-ui.field>
        <div class="grid gap-4 sm:grid-cols-2">
            <x-ui.field :label="__('Module style')" for="demo-style">
                <x-ui.select id="demo-style" wire:model.live="style">
                    <option value="square">{{ __('Square') }}</option>
                    <option value="dot">{{ __('Dots') }}</option>
                    <option value="round">{{ __('Rounded') }}</option>
                </x-ui.select>
            </x-ui.field>
            <x-ui.field :label="__('Corner style')" for="demo-eye">
                <x-ui.select id="demo-eye" wire:model.live="eye">
                    <option value="square">{{ __('Square') }}</option>
                    <option value="circle">{{ __('Circle') }}</option>
                </x-ui.select>
            </x-ui.field>
            <x-ui.color-input model="color" id="demo-color" :label="__('Code colour')" />
            <x-ui.color-input model="bg" id="demo-bg" :label="__('Background')" />
        </div>
        @if ($limited)
            <x-ui.alert type="warning">{{ __('You are going a bit fast. Wait a minute and try again.') }}</x-ui.alert>
        @endif
    </div>
    <div class="flex flex-col items-center gap-4">
        <div class="qr-stage relative rounded-2xl p-6">
            <img src="{{ $preview }}" alt="{{ __('Preview of your QR code') }}" class="size-56 rounded-md shadow-raised transition-opacity duration-200 sm:size-64" wire:loading.class="opacity-40">
            <span wire:loading.flex class="absolute inset-0 hidden items-center justify-center" aria-hidden="true">
                <span class="size-8 animate-spin rounded-full border-2 border-primary border-t-transparent"></span>
            </span>
        </div>
        <x-ui.button variant="secondary" icon="heroicon-o-arrow-down-tray" wire:click="download">{{ __('Download SVG') }}</x-ui.button>
    </div>
</div>

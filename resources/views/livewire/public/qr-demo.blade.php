<div class="grid items-center gap-8 rounded-xl border border-line bg-surface p-6 shadow-card sm:p-8 lg:grid-cols-[1fr_auto]">
    <div class="space-y-5">
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
        <div class="qr-stage rounded-lg p-5">
            <img src="{{ $preview }}" alt="{{ __('Preview of your QR code') }}" class="size-56 rounded-sm shadow-card" wire:loading.class="opacity-50">
        </div>
        <x-ui.button variant="secondary" icon="heroicon-o-arrow-down-tray" wire:click="download">{{ __('Download SVG') }}</x-ui.button>
    </div>
</div>

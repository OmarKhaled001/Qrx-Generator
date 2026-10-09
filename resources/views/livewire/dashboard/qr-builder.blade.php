<div>
    <x-ui.page-header
        :heading="$qrCode ? __('Edit QR code') : __('Create QR code')"
        :description="$qrCode ? __('Changes apply to printed codes instantly.') : __('Pick a type, add your content and style it.')"
    >
        <x-slot:actions>
            <x-ui.button :href="$qrCode ? route('qr.show', $qrCode) : route('qr.index')" variant="secondary" wire:navigate>{{ __('Cancel') }}</x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($atLimit)
        <x-ui.alert type="warning" :heading="__('You have reached the limit of your plan.')" class="mb-6">
            {{ __('Your :plan plan includes :limit QR codes.', ['plan' => __($plan->name), 'limit' => Number::format($plan->qr_limit)]) }}
            @if (Route::has('billing'))
                <a href="{{ route('billing') }}" class="font-semibold underline">{{ __('Upgrade your plan') }}</a>
            @endif
        </x-ui.alert>
    @endif
    @error('quota')<x-ui.alert type="danger" class="mb-6">{{ $message }}</x-ui.alert>@enderror

    <form wire:submit="save" class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            <x-ui.card :heading="__('1. Type')">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4" role="radiogroup" aria-label="{{ __('QR code type') }}">
                    @foreach ($types as $option)
                        <button
                            type="button"
                            role="radio"
                            aria-checked="{{ $type === $option->value ? 'true' : 'false' }}"
                            wire:click="selectType('{{ $option->value }}')"
                            @class([
                                'flex flex-col items-start gap-2 rounded-md border p-3 text-start transition',
                                'border-primary bg-primary-soft ring-1 ring-primary' => $type === $option->value,
                                'border-line hover:border-line-strong hover:bg-sunken' => $type !== $option->value,
                            ])
                        >
                            <x-dynamic-component :component="$option->icon()" @class(['size-5', 'text-primary-ink' => $type === $option->value, 'text-ink-muted' => $type !== $option->value]) aria-hidden="true" />
                            <span class="text-sm font-medium text-ink">{{ $option->label() }}</span>
                        </button>
                    @endforeach
                </div>
                <p class="mt-3 text-sm text-ink-muted">{{ \App\Enums\QrType::from($type)->description() }}</p>
            </x-ui.card>

            <x-ui.card :heading="__('2. Content')">
                <div class="space-y-5">
                    <x-ui.field :label="__('Name')" for="name" :hint="__('Shown only to you.')" required>
                        <x-ui.input name="name" wire:model.blur="name" :placeholder="__('Restaurant menu')" maxlength="120" />
                    </x-ui.field>
                    @include('livewire.dashboard.qr-fields')
                </div>
            </x-ui.card>

            <x-ui.card :heading="__('3. Design')">
                <div class="grid gap-5 sm:grid-cols-2">
                    <x-ui.field :label="__('Module style')" for="design.style">
                        <x-ui.select name="design.style" wire:model.live="design.style">
                            <option value="square">{{ __('Square') }}</option>
                            <option value="dot">{{ __('Dots') }}</option>
                            <option value="round">{{ __('Rounded') }}</option>
                        </x-ui.select>
                    </x-ui.field>
                    <x-ui.field :label="__('Corner style')" for="design.eye">
                        <x-ui.select name="design.eye" wire:model.live="design.eye">
                            <option value="square">{{ __('Square') }}</option>
                            <option value="circle">{{ __('Circle') }}</option>
                        </x-ui.select>
                    </x-ui.field>

                    <x-ui.checkbox id="use-gradient" wire:model.live="useGradient" :label="__('Use a gradient')" class="sm:col-span-2" />
                    @if ($useGradient)
                        <x-ui.color-input model="design.gradient.from" id="gradient-from" :label="__('Gradient start')" />
                        <x-ui.color-input model="design.gradient.to" id="gradient-to" :label="__('Gradient end')" />
                    @else
                        <x-ui.color-input model="design.color" id="design-color" :label="__('Code colour')" />
                    @endif
                    <x-ui.color-input model="design.bg" id="design-bg" :label="__('Background')" />

                    <x-ui.field :label="__('Quiet zone')" for="design.margin" :hint="__('Empty border around the code. 1–4 scans best.')">
                        <x-ui.select name="design.margin" wire:model.live="design.margin">
                            @foreach (range(0, 10) as $margin)
                                <option value="{{ $margin }}">{{ Number::format($margin) }}</option>
                            @endforeach
                        </x-ui.select>
                    </x-ui.field>
                </div>
            </x-ui.card>
        </div>

        <aside class="lg:sticky lg:top-24 lg:self-start">
            <x-ui.card :heading="__('Preview')">
                <div class="qr-stage grid place-items-center rounded-md p-6">
                    <img src="{{ $preview }}" alt="{{ __('Preview of your QR code') }}" class="aspect-square w-full max-w-64 rounded-sm shadow-card" wire:loading.class="opacity-50" wire:target="design, useGradient">
                </div>
                @if ($lowContrast)
                    <x-ui.alert type="warning" class="mt-4">{{ __('Low contrast between the code and the background may make it hard to scan.') }}</x-ui.alert>
                @endif
                <p class="mt-4 text-xs text-ink-subtle">
                    {{ $qrCode ? __('This code points to :url', ['url' => $qrCode->shortUrl()]) : __('Your code gets its own short link when you save it.') }}
                </p>
                <x-ui.button type="submit" class="mt-5 w-full" :disabled="$atLimit" wire:loading.attr="disabled" wire:target="save">
                    <span wire:loading.remove wire:target="save">{{ $qrCode ? __('Save changes') : __('Create QR code') }}</span>
                    <span wire:loading wire:target="save">{{ __('Saving…') }}</span>
                </x-ui.button>
            </x-ui.card>
        </aside>
    </form>
</div>

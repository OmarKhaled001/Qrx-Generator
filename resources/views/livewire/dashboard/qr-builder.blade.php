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
            <x-ui.card :heading="__('Type')" icon="heroicon-o-squares-2x2">
                <div class="grid grid-cols-2 gap-2 sm:grid-cols-4" role="radiogroup" aria-label="{{ __('QR code type') }}">
                    @foreach ($types as $option)
                        <button
                            type="button"
                            role="radio"
                            aria-checked="{{ $type === $option->value ? 'true' : 'false' }}"
                            wire:click="selectType('{{ $option->value }}')"
                            @class([
                                'relative flex flex-col items-start gap-2.5 rounded-lg border p-3.5 text-start transition-[border-color,background-color,box-shadow,transform] duration-150 active:scale-[0.98]',
                                'border-primary bg-primary-soft shadow-xs ring-1 ring-primary' => $type === $option->value,
                                'border-line hover:-translate-y-px hover:border-line-strong hover:bg-sunken hover:shadow-xs' => $type !== $option->value,
                            ])
                        >
                            <x-dynamic-component :component="$option->icon()" @class(['size-5', 'text-primary-ink' => $type === $option->value, 'text-ink-muted' => $type !== $option->value]) aria-hidden="true" />
                            <span class="text-sm font-medium text-ink">{{ $option->label() }}</span>
                            @if ($type === $option->value)
                                <x-heroicon-s-check-circle class="absolute end-2.5 top-2.5 size-4 text-primary" aria-hidden="true" />
                            @endif
                        </button>
                    @endforeach
                </div>
                <p class="mt-3 text-sm text-ink-muted">{{ \App\Enums\QrType::from($type)->description() }}</p>
            </x-ui.card>

            <x-ui.card :heading="__('Content')" icon="heroicon-o-pencil-square">
                <div class="space-y-5">
                    <x-ui.field :label="__('Name')" for="name" :hint="__('Shown only to you.')" required>
                        <x-ui.input name="name" wire:model.blur="name" :placeholder="__('Restaurant menu')" maxlength="120" />
                    </x-ui.field>
                    @include('livewire.dashboard.qr-fields')
                </div>
            </x-ui.card>

            <x-ui.card :heading="__('Design')" icon="heroicon-o-swatch">
                <fieldset>
                    <legend class="text-sm font-medium text-ink">{{ __('Start from a style') }}</legend>
                    <div class="mt-2 flex flex-wrap gap-2">
                        @foreach ($presets as $key => $preset)
                            @php($selected = $design['style'] === $preset['style'] && $design['eye'] === $preset['eye'] && strcasecmp($design['bg'], $preset['bg']) === 0 && ($preset['gradient'] ? $useGradient && strcasecmp($design['gradient']['from'], $preset['gradient']['from']) === 0 : ! $useGradient && strcasecmp($design['color'], $preset['color']) === 0))
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

                <div class="mt-6 grid gap-5 border-t border-line pt-6 sm:grid-cols-2">
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

                    <div class="sm:col-span-2"><x-ui.checkbox id="use-gradient" wire:model.live="useGradient" :label="__('Use a gradient')" /></div>
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
            <x-ui.card :heading="__('Preview')" icon="heroicon-o-eye">
                <div class="qr-stage relative grid place-items-center rounded-lg p-6">
                    <img src="{{ $preview }}" alt="{{ __('Preview of your QR code') }}" class="aspect-square w-full max-w-64 rounded-md shadow-raised transition-opacity duration-200" wire:loading.class="opacity-40" wire:target="design, useGradient, applyPreset">
                    <span wire:loading.flex wire:target="design, useGradient, applyPreset" class="absolute inset-0 hidden items-center justify-center" aria-hidden="true">
                        <span class="size-8 animate-spin rounded-full border-2 border-primary border-t-transparent"></span>
                    </span>
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

        {{-- Small screens: the preview card sits below the whole form, so keep a live thumbnail and the save button in reach. --}}
        <div class="h-20 lg:hidden" aria-hidden="true"></div>
        <div class="fixed inset-x-0 bottom-0 z-20 flex items-center gap-3 border-t border-line bg-surface/90 px-4 py-3 shadow-pop backdrop-blur-lg lg:hidden">
            <img src="{{ $preview }}" alt="" class="size-14 shrink-0 rounded-md bg-white p-1 ring-1 ring-line" wire:loading.class="opacity-40" wire:target="design, useGradient, applyPreset">
            <p class="min-w-0 flex-1 truncate text-sm text-ink-muted">{{ $name !== '' ? $name : __('Preview') }}</p>
            <x-ui.button type="submit" :disabled="$atLimit" wire:loading.attr="disabled" wire:target="save">
                {{ $qrCode ? __('Save changes') : __('Create QR code') }}
            </x-ui.button>
        </div>
    </form>
</div>

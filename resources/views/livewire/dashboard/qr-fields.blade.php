{{-- Content fields for the selected QR type. Bound to $payload in QrBuilder. --}}
@switch($type)
    @case('url')
        <x-ui.field :label="__('Website address')" for="payload.url" :hint="__('Must start with http:// or https://')" required>
            <x-ui.input name="payload.url" type="url" wire:model.live.debounce.400ms="payload.url" :placeholder="__('https://example.com/menu')" dir="ltr" required />
        </x-ui.field>
        @break

    @case('text')
        <x-ui.field :label="__('Text')" for="payload.text" required>
            <x-ui.textarea name="payload.text" wire:model.live.debounce.400ms="payload.text" rows="4" maxlength="1000" />
        </x-ui.field>
        @break

    @case('phone')
        <x-ui.field :label="__('Phone number')" for="payload.phone" required>
            <x-ui.input name="payload.phone" type="tel" wire:model.live.debounce.400ms="payload.phone" :placeholder="__('+20 100 000 0000')" dir="ltr" />
        </x-ui.field>
        @break

    @case('sms')
        <x-ui.field :label="__('Phone number')" for="payload.phone" required>
            <x-ui.input name="payload.phone" type="tel" wire:model.live.debounce.400ms="payload.phone" :placeholder="__('+20 100 000 0000')" dir="ltr" />
        </x-ui.field>
        <x-ui.field :label="__('Message')" for="payload.message" :hint="__('Optional. People can edit it before sending.')">
            <x-ui.textarea name="payload.message" wire:model.live.debounce.400ms="payload.message" rows="3" maxlength="500" />
        </x-ui.field>
        @break

    @case('email')
        <x-ui.field :label="__('Email address')" for="payload.email" required>
            <x-ui.input name="payload.email" type="email" wire:model.live.debounce.400ms="payload.email" dir="ltr" />
        </x-ui.field>
        <x-ui.field :label="__('Subject')" for="payload.subject">
            <x-ui.input name="payload.subject" wire:model.live.debounce.400ms="payload.subject" />
        </x-ui.field>
        <x-ui.field :label="__('Body')" for="payload.body">
            <x-ui.textarea name="payload.body" wire:model.live.debounce.400ms="payload.body" rows="4" />
        </x-ui.field>
        @break

    @case('vcard')
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.field :label="__('Full name')" for="payload.full_name" class="sm:col-span-2" required>
                <x-ui.input name="payload.full_name" wire:model.live.debounce.400ms="payload.full_name" autocomplete="off" />
            </x-ui.field>
            <x-ui.field :label="__('Organization')" for="payload.organization">
                <x-ui.input name="payload.organization" wire:model.live.debounce.400ms="payload.organization" />
            </x-ui.field>
            <x-ui.field :label="__('Job title')" for="payload.title">
                <x-ui.input name="payload.title" wire:model.live.debounce.400ms="payload.title" />
            </x-ui.field>
            <x-ui.field :label="__('Phone number')" for="payload.phone">
                <x-ui.input name="payload.phone" type="tel" wire:model.live.debounce.400ms="payload.phone" dir="ltr" />
            </x-ui.field>
            <x-ui.field :label="__('Work phone')" for="payload.tel">
                <x-ui.input name="payload.tel" type="tel" wire:model.live.debounce.400ms="payload.tel" dir="ltr" />
            </x-ui.field>
            <x-ui.field :label="__('Email address')" for="payload.email">
                <x-ui.input name="payload.email" type="email" wire:model.live.debounce.400ms="payload.email" dir="ltr" />
            </x-ui.field>
            <x-ui.field :label="__('Website')" for="payload.website">
                <x-ui.input name="payload.website" type="url" wire:model.live.debounce.400ms="payload.website" dir="ltr" />
            </x-ui.field>
            <x-ui.field :label="__('Address')" for="payload.address" class="sm:col-span-2">
                <x-ui.input name="payload.address" wire:model.live.debounce.400ms="payload.address" />
            </x-ui.field>
            <x-ui.field :label="__('Note')" for="payload.note" class="sm:col-span-2">
                <x-ui.textarea name="payload.note" wire:model.live.debounce.400ms="payload.note" rows="2" />
            </x-ui.field>
        </div>
        @break

    @case('wifi')
        <div class="grid gap-5 sm:grid-cols-2">
            <x-ui.field :label="__('Network name')" for="payload.ssid" class="sm:col-span-2" required>
                <x-ui.input name="payload.ssid" wire:model.live.debounce.400ms="payload.ssid" dir="auto" />
            </x-ui.field>
            <x-ui.field :label="__('Security')" for="payload.encryption">
                <x-ui.select name="payload.encryption" wire:model.live="payload.encryption">
                    <option value="WPA">{{ __('WPA / WPA2 / WPA3') }}</option>
                    <option value="WEP">{{ __('WEP (old)') }}</option>
                    <option value="nopass">{{ __('Open network (no password)') }}</option>
                </x-ui.select>
            </x-ui.field>
            @if (($payload['encryption'] ?? 'WPA') !== 'nopass')
                <x-ui.field :label="__('Password')" for="payload.password" required>
                    <x-ui.input name="payload.password" wire:model.live.debounce.400ms="payload.password" autocomplete="off" dir="ltr" />
                </x-ui.field>
            @endif
            <div class="sm:col-span-2"><x-ui.checkbox id="payload-hidden" wire:model.live="payload.hidden" :label="__('Hidden network')" /></div>
        </div>
        @break

    @case('location')
        <div class="grid gap-5 sm:grid-cols-2" x-data="{ locating: false, failed: false }">
            <x-ui.field :label="__('Place name')" for="payload.label" class="sm:col-span-2" :hint="__('Optional, shown to people who scan.')">
                <x-ui.input name="payload.label" wire:model.live.debounce.400ms="payload.label" />
            </x-ui.field>
            <x-ui.field :label="__('Latitude')" for="payload.latitude" required>
                <x-ui.input name="payload.latitude" inputmode="decimal" wire:model.live.debounce.400ms="payload.latitude" :placeholder="__('30.0444')" dir="ltr" />
            </x-ui.field>
            <x-ui.field :label="__('Longitude')" for="payload.longitude" required>
                <x-ui.input name="payload.longitude" inputmode="decimal" wire:model.live.debounce.400ms="payload.longitude" :placeholder="__('31.2357')" dir="ltr" />
            </x-ui.field>
            <div class="sm:col-span-2">
                <x-ui.button
                    variant="secondary"
                    size="sm"
                    icon="heroicon-o-map-pin"
                    x-on:click="locating = true; failed = false; navigator.geolocation.getCurrentPosition(
                        (p) => { $wire.set('payload.latitude', p.coords.latitude.toFixed(6)); $wire.set('payload.longitude', p.coords.longitude.toFixed(6)); locating = false },
                        () => { locating = false; failed = true }
                    )"
                    x-bind:disabled="locating"
                >{{ __('Use my current location') }}</x-ui.button>
                <p x-show="failed" x-cloak class="mt-2 text-xs text-danger-600" role="alert">{{ __('Could not get your location. Enter the coordinates instead.') }}</p>
            </div>
        </div>
        @break
@endswitch
